<?php

namespace Classes;

use Exception;
use \PDO;

class Package
{
    public $conn;
    public $ds;
    function __construct()
    {
    }
    function initDS()
    {
        $this->ds = new DataService();
        $this->conn = $this->ds->conn;
    }

    function endPackage($packageId)
    {
        try {
            $this->initDS();
            $stmt2 =  $this->conn->prepare("UPDATE  packages set end_date=CURDATE(), status=0 where id_package=:id");
            $stmt2->bindparam(":id", $packageId, PDO::PARAM_INT);
            return ($stmt2->execute());
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    function getFreePackage($packageId, $accountId)
    {
        try {
            $this->initDS();
            $feedBack = array();
            $pack = $this->conn->prepare("SELECT *, (SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select oc.id_customer from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount from packages p where p.status=1 and (p.id_website is null or p.id_website=:IDW) and p.id_package=:IDP");
            $pack->bindParam(':IDC', $accountId, PDO::PARAM_INT);
            $pack->bindParam(':IDP', $packageId, PDO::PARAM_INT);
            $pack->execute();
            $package = $pack->fetch();
            if ($package) {
                $updateBalance = $this->conn->prepare("UPDATE customers set balance=balance+:BAL where id_customer=:IDC");
                $updateBalance->bindParam(':IDC', $accountId, PDO::PARAM_INT);
                $updateBalance->bindParam(':BAL', $package["messages"], PDO::PARAM_INT);
                $updateBalance->execute();
                $feedBack["status"] = 200;
                $feedBack["message"] =  "Package Added successfully.";
                $feedBack["code"] = 200;
                return $feedBack;
            } else {
                $feedBack["status"] = 201;
                $feedBack["message"] =  "Package not found or customer not eligible for this package.";
                $feedBack["code"] = 202;
                return $feedBack;
            }
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    function buyPackage($packageId, $accountId, $websiteId)
    {
        try {
            $this->initDS();
            $feedBack = array();
            $pack = $this->conn->prepare("SELECT *, (SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select oc.id_customer from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount from packages p where p.status=1 and (p.id_website is null or p.id_website=:IDW) and p.id_package=:IDP");
            $pack->bindParam(':IDC', $accountId, PDO::PARAM_INT);
            $pack->bindParam(':IDP', $packageId, PDO::PARAM_INT);
            $pack->bindParam(':IDW', $websiteId, PDO::PARAM_INT);
            $pack->execute();
            $package = $pack->fetch();
            if ($package) {
                $final_price = (100 - $package["total_discount"]) * ($package["price"] / 100);
                $trans = $this->conn->prepare("INSERT into transactionsc(`id_customer`, `id_package`, `status`, `confirmation`, `date_add`, `final_price`) 
                                                 VALUES(:IDC,:IDP,1,0,CURDATE(),:FPR)");
                $trans->bindParam(':IDC', $accountId, PDO::PARAM_INT);
                $trans->bindParam(':IDP', $packageId, PDO::PARAM_INT);
                $trans->bindParam(':FPR', $final_price, PDO::PARAM_INT);
                if ($trans->execute()) {
                    $updateBalance = $this->conn->prepare("UPDATE customers set balance=balance+:BAL where id_customer=:IDC");
                    $updateBalance->bindParam(':IDC', $accountId, PDO::PARAM_INT);
                    $updateBalance->bindParam(':BAL', $package["messages"], PDO::PARAM_INT);
                    $updateBalance->execute();
                    $feedBack["status"] = 200;
                    $feedBack["message"] =  "Package buyed successfully.";
                    $feedBack["code"] = 200;
                    return $feedBack;
                } else {
                    $feedBack["status"] = 201;
                    $feedBack["message"] =  "Operation failed, please try again later.";
                    $feedBack["code"] = 203;
                    return $feedBack;
                }
            } else {
                $feedBack["status"] = 201;
                $feedBack["message"] =  "Package not found or customer not eligible for this package.";
                $feedBack["code"] = 202;
                return $feedBack;
            }
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }



    function requestPackagePaymentinformation($packageId, $accountId, $websiteId, $currency)
    {
        try {
            $this->initDS();
            $feedBack = array();
            $pack = $this->conn->prepare("SELECT p.*,pp.price,pp.currency, (SELECT sum(discount) from offers o where ((o.start_date <= CURRENT_DATE and o.end_date >= CURRENT_DATE) or (o.start_date is null and o.end_date is null)) and o.id_package=p.id_package and (o.access=1 or FIND_IN_SET(:IDC,(select oc.id_customer from offers_customers oc where o.id_offer=oc.id_offer)))) as total_discount  from packages p JOIN packages_price pp ON  pp.id_package=p.id_package where  pp.primary=1 AND p.status=1 and (p.id_website is null or p.id_website=:IDW) and p.id_package=:IDP");
            $pack->bindParam(':IDC', $accountId, PDO::PARAM_INT);
            $pack->bindParam(':IDP', $packageId, PDO::PARAM_INT);
            $pack->bindParam(':IDW', $websiteId, PDO::PARAM_INT);
            $pack->execute();
            $package = $pack->fetch();
            if ($package) {
                $c = $this->conn->prepare("SELECT * from customers where id_customer=:IDC");
                $c->bindParam(':IDC', $accountId, PDO::PARAM_INT);
                $c->execute();
                $customer = $c->fetch();

                $a = $this->conn->prepare("SELECT * from accounts where id_account=:IDA");
                $a->bindParam(':IDA', $package["id_account"], PDO::PARAM_INT);
                $a->execute();
                $account = $a->fetch();
                $final_price = (100 - $package["total_discount"]) * ($package["price"] / 100);
                $payment_requirement = array();
                $payment_requirement["id_company"] = $account["id_company"];
                $payment_requirement["id_shop"] = $account["id_shop"];
                $payment_requirement["amount"] = $final_price;
                $payment_requirement["currency"] = $package["currency"];
                $payment_requirement["country"] = $customer["country"];
                $payment_requirement["last_name"] = $customer["lastname"];
                $payment_requirement["first_name"] = $customer["firstname"];
                $payment_requirement["email"] = $customer["emailc"];
                $feedBack["status"] = 200;
                $feedBack["message"] =  "payment data found.";
                $feedBack["payment_requirement"] = $payment_requirement;
                return $feedBack;
            } else {
                $feedBack["status"] = 201;
                $feedBack["message"] =  "Package not found or customer not eligible for this package.";
                $feedBack["code"] = 202;
                return $feedBack;
            }
        } catch (Exception $e) {
            return 'erreur';
            die('Erreur : ' . $e->getMessage());
        }
    }
}
