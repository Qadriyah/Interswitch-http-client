<?php

namespace App\Services;

use App\Http\HttpClient\QuickTellerAPIClient;
use App\Repositories\QuickTellerAPIInterface;
use Illuminate\Support\Collection;

class QuickTellerAPIService implements QuickTellerAPIInterface
{

    /**
     * Retrieves all categories and filters out the required categories
     * @return array
     */
    public function getCategories()
    {
        $endpoint = 'categorys';
        $auth = new QuickTellerAPIClient(env('BASE_API_URL'));
        $data = $auth->quickTeller($endpoint)['categorys'];

        return [
            'categorys' => $this->addBillerIds($this->getRequiredCategories($data))
        ];
    }

    /**
     * Retrieves all the billers and filters out the required billers
     * @return array
     */
    public function getBillers()
    {
        $endpoint = 'billers';
        $auth = new QuickTellerAPIClient(env('BASE_API_URL'));
        $billers = $auth->quickTeller($endpoint)['billers'];
        return [
            'billers' => $this->getRequiredCategories($billers),
        ];
    }

    /**
     * Retrieves payment items for a particular biller
     * @param $billerId
     * @return mixed
     */
    public function getPaymentItems($billerId)
    {
        $endpoint = "billers/{$billerId}/paymentitems";
        $auth = new QuickTellerAPIClient(env('BASE_API_URL'));
        return $auth->quickTeller($endpoint);
    }

    /**
     * Filers out the required categories
     * @param $categories
     * @return Collection
     */
    public function getRequiredCategories($categories)
    {
        return collect(config('categories'))->map(function ($value, $key) use ($categories) {
            return collect($categories)->filter(function ($category) use ($key, $value) {
                return ($category['categoryid'] === (string)$key);
            });
        })->flatten(1);
    }

    /**
     * Combines billers and payment items
     * @param $billers
     * @return Collection
     */
    public function addPaymentItems($billers)
    {
        return collect($billers)->map(function ($biller) {
            $paymentItems = $this->getPaymentItems($biller['billerid'])['paymentitems'];
            $biller['paymentitems'] = $paymentItems;
            return $biller;
        });
    }

    public function addBillerIds($categories)
    {
        return $categories->map(function ($category) {
            $category['billerId'] = config('categories')[$category['categoryid']];
            return $category;
        });
    }

    public function encryptData()
    {
        $stream = fopen('/Users/baker/Documents/aone/quick-teller/privateKey.pem', 'r');
        $privatekey = fread($stream, 2048);
        $res = openssl_get_privatekey($privatekey);

        $st = openssl_private_encrypt("HI BAKER", $encrypted, $res, OPENSSL_PKCS1_PADDING);
        dump($encrypted);

        $stream1 = fopen('/Users/baker/Documents/aone/quick-teller/publicKey.pem', 'r');
        $publickey = fread($stream1, 2048);
        $res1 = openssl_get_publickey($publickey);

        $st1 = openssl_public_decrypt($encrypted, $rowData, $res1);
        dd($rowData);
    }
}

