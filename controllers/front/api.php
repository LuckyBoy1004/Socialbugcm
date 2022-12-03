<?php
/**
 * @author SocialBug Team <support@mlm-socialbug.com>
 * @copyright 2019 KM Innovations Inc
 * @license https://www.gnu.org/licenses/gpl-2.0.html
 */

use PrestaShop\PrestaShop\Adapter\ContainerBuilder;

class SocialbugcrmApiModuleFrontController extends Controller
{
    private $apiKey;

    public function init()
    {
        $this->content_only = true;
    }

    protected function buildContainer()
    {
    }

    public function checkAccess()
    {
        $headers = WebserviceRequest::getallheaders();
        
        if (!isset($headers['Api-Key'])) {
            return false;
        }

        $api_key = Configuration::get('SOCIALBUGCRM_ApiKey');
        return strcasecmp($headers['Api-Key'], $api_key) == 0;
    }

    public function viewAccess()
    {
        return true;
    }

    public function postProcess()
    {
        return false;
    }

    public function display()
    {
    }

    public function setMedia()
    {
    }

    public function initHeader()
    {
    }

    public function initContent()
    {
        header('Content-Type: application/json; charset=utf-8');

        Context::getContext()->cart = new Cart();

        try {
            $module_action = Tools::getValue('module_action');

            $action_list = array(
                'HelloWorld' => 'helloWorldAction',
                'GetStoreInfo' => 'getStoreInfoAction',
                'PostStoreInfo' => 'postStoreInfoAction',
                'PostSalt' => 'postSaltAction',
                'GetCustomerByEmail' => 'getCustomerByEmailAction',
                'AddCustomer' => 'postAddCustomerAction'
            );

            if (isset($action_list[$module_action])) {
                $action = $action_list[$module_action];
            }

            $this->$action();
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
        }
    }
    
    public function initCursedPage()
    {
        header('HTTP/1.1 401 Unauthorized');
        die;
    }

    public function initFooter()
    {
    }

    protected function redirect()
    {
    }

    public function helloWorldAction()
    {
        echo 1;
    }

    public function getStoreInfoAction()
    {
        $userId = Configuration::get('SOCIALBUGCRM_UserId');

        $integration = array();

        $integration['ApiKey'] = Configuration::get('SOCIALBUGCRM_ApiKey');
        $integration['Salt'] = Configuration::get('SOCIALBUGCRM_Salt');
        $integration['CreatedOnUtc'] = Configuration::get('SOCIALBUGCRM_CreatedOnUtc');
        $integration['UserId'] = $userId;
        $integration['AppendHtml'] = Configuration::get('SOCIALBUGCRM_AppendHtml');

        $employee = new Employee($userId);

        $integration['Email'] = $employee->email;
        $integration['FirstName'] = $employee->firstname;
        $integration['LastName'] = $employee->lastname;

        echo json_encode($integration);
        //die(Tools::jsonEncode($integration));
    }

    public function postStoreInfoAction()
    {
        $input_data = null;

        if (($_SERVER['REQUEST_METHOD'] == 'POST')) {
            $postresource = fopen("php://input", "r");
            while ($postData = fread($postresource, 1024)) {
                $input_data .= $postData;
            }
            fclose($postresource);
        }

        $result = 0;

        if (isset($input_data)) {
            $appendHtml = Configuration::get('SOCIALBUGCRM_AppendHtml');

            if ($appendHtml != $input_data) {
                Configuration::updateValue('SOCIALBUGCRM_AppendHtml', $input_data);
                $result = 1;
            }
        }

        echo $result;
    }

    public function postSaltAction()
    {
        $input_data = null;

        if (($_SERVER['REQUEST_METHOD'] == 'POST')) {
            $postresource = fopen("php://input", "r");
            while ($postData = fread($postresource, 1024)) {
                $input_data .= $postData;
            }
            fclose($postresource);
        }

        $result = 0;

        if (isset($input_data)) {
            $salt = Configuration::get('SOCIALBUGCRM_Salt');

            if ($salt != $input_data) {
                Configuration::updateValue('SOCIALBUGCRM_Salt', $input_data);
                $result = 1;
            }
        }

        echo $result;
    }

    public function getCustomerByEmailAction()
    {
        $email = (string)Tools::getValue('email', false);
        $id_lang = $this->context->language->id;

        $customer = new Customer();
        $customer = $customer->getByEmail($email);

        if ($customer != null && $customer->id) {
            $result = $this->makeCustomerData($customer, $id_lang);

            echo json_encode($result);
        } else {
            header('HTTP/1.1 400 Bad Request');
        }
    }

    private function makeCustomerData($customer, $id_lang)
    {
        $id_address_s = $this->getAddressIdByAlias($customer->id, 'DefaultShipping');
        $id_address_b = $this->getAddressIdByAlias($customer->id, 'DefaultBilling');

        if ($id_address_b == 0) {
            $id_address_b = Address::getFirstCustomerAddressId($customer->id);
        }

        if ($id_address_s == 0) {
            $id_address_s = Address::getFirstCustomerAddressId($customer->id);
        }

        return $this->makeCustomerDataWithAddress($customer, $id_address_b, $id_address_s, $id_lang);
    }

    private function getAddressIdByAlias($id_customer, $alias)
    {
        if (!$id_customer) {
            return false;
        }

        $result = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `id_address`
				FROM `'._DB_PREFIX_.'address`
				WHERE `id_customer` = '.(int)$id_customer.' AND `deleted` = 0 AND `alias` = "'.pSQL($alias).'"');

        return $result;
    }

    private function makeCustomerDataWithAddress($customer, $id_billing_address, $id_shipping_address, $id_lang)
    {
        $customerData = new stdClass();
        $customerData->CustomerId = (int)$customer->id;
        $customerData->UserName = $customer->firstname.' '.$customer->lastname;
        $customerData->Email = $customer->email;
        $customerData->BillingAddress = $this->makeAddressData($id_billing_address, $customer->email, $id_lang);
        $customerData->ShippingAddress = $this->makeAddressData($id_shipping_address, $customer->email, $id_lang);
        $customerData->AffiliateId = null;

        return $customerData;
    }

    private function makeAddressData($id_address, $email, $id_lang)
    {
        $address = new Address((int)$id_address, $id_lang);
        $addressData = new stdClass();
        $addressData->FirstName = $address->firstname;
        $addressData->LastName = $address->lastname;
        $addressData->Email = $email;
        $addressData->Company = $address->company;
        $addressData->CountryId =(int)$address->id_country;
        $addressData->StateProvinceId = (int)$address->id_state;
        $addressData->City = $address->city;
        $addressData->Address1 = $address->address1;
        $addressData->Address2 = $address->address2;
        $addressData->ZipPostalCode = $address->postcode;
        $addressData->PhoneNumber = $address->phone;

        return $addressData;
    }

    public function postAddCustomerAction()
    {
        $input_json = null;
        $id_lang = $this->context->language->id;

        if (($_SERVER['REQUEST_METHOD'] == 'POST')) {
            $postresource = fopen("php://input", "r");
            while ($postData = fread($postresource, 1024)) {
                $input_json .= $postData;
            }
            fclose($postresource);
        }
        
        if (isset($input_json)) {
            $data = json_decode($input_json, 1);
            $data = $data['Customer'];

            $found_customer = $this->getOrNewCustomer($data);
        }

        $result = $this->makeCustomerData($found_customer, $id_lang);

        echo json_encode($result);
    }

    private function getOrNewCustomer($data)
    {
        $email = $data['Email'];

        $customer = new Customer();
        $found_customer = $customer->getByEmail($email);

        if (!$found_customer || !$found_customer->id) {
            $this->addNewCustomer($data);

            $customer = new Customer();
            $found_customer = $customer->getByEmail($email);
        }

        return $found_customer;
    }

    private function addNewCustomer($data)
    {
        $customer = new Customer();
        $customer->email = $data['Email'];
        $addr_s = $data['ShippingAddress'];
        $addr_b = $data['BillingAddress'];

        $customer->firstname = 'Unknown';
        $customer->lastname = 'User';

        if ($data['UserName'] != '') {
            $userName = explode(' ', $data['UserName']);
            if ($userName[0] != '') {
                $customer->firstname = $userName[0];
            }
            if ($userName[1] != '') {
                $customer->lastname = $userName[1];
            }
        } elseif ($addr_s['FirstName'] != '') {
            $customer->firstname = $addr_s['FirstName'];
            $customer->lastname = $addr_s['LastName'];
        } elseif ($addr_b['FirstName'] != '') {
            $customer->firstname = $addr_b['FirstName'];
            $customer->lastname = $addr_b['LastName'];
        }

        if ($data['Password'] != '') {
            $customer->setWsPasswd($data['Password']);
        }

        $customer->add();


        if ($addr_s['FirstName'] != '') {
            $data['ShippingAddress']['Id'] = $this->getOrNewAddress($addr_s, $customer);
        }

        if ($addr_b['FirstName'] != '') {
            $data['BillingAddress']['Id'] = $this->getOrNewAddress($addr_b, $customer);
        } else {
            $data['BillingAddress']['Id'] = $data['ShippingAddress']['Id'];
        }

        return $customer;
    }

    private function getOrNewAddress($addr, $customer)
    {
        $addresses = $customer->getSimpleAddresses();
        foreach ($addresses as $address) {
            if ($address['alias'] == "Default"
            && $address['firstname'] == $addr['FirstName']
            && $address['lastname'] == $addr['LastName']
            && $address['company'] == (empty($addr['Company']) ? "" : $addr['Company'])
            && $address['id_country'] == (empty($addr['CountryId']) ? 0 : $addr['CountryId'])
            && $address['id_state'] == (empty($addr['StateProvinceId']) ? 0 : $addr['StateProvinceId'])
            && $address['city'] == (empty($addr['City']) ? "" : $addr['City'])
            && $address['address1'] == (empty($addr['Address1']) ? "" : $addr['Address1'])
            && $address['address2'] == (empty($addr['Address2']) ? "" : $addr['Address2'])
            && $address['postcode'] == (empty($addr['ZipPostalCode']) ? "" : $addr['ZipPostalCode'])
            && $address['phone'] == (empty($addr['PhoneNumber']) ? "" : $addr['PhoneNumber'])) {
                return $address['id'];
            }
        }

        return $this->addAddress($addr, 'Default', $customer->id);
    }

    private function addAddress($addr, $alias, $customerId = null)
    {
        $address = new Address();

        $address->alias = $alias;
        $address->firstname = $addr['FirstName'];
        $address->lastname = $addr['LastName'];
        $address->company = $addr['Company'];
        $address->id_country = $addr['CountryId'];
        $address->id_state = $addr['StateProvinceId'];
        $address->city = $addr['City'];
        $address->address1 = $addr['Address1'];
        $address->address2 = $addr['Address2'];
        $address->postcode = $addr['ZipPostalCode'];
        $address->phone = $addr['PhoneNumber'];
        
        if ($customerId != null) {
            $address->id_customer = $customerId;
        }

        $address->add();

        return $address->id;
    }
}
