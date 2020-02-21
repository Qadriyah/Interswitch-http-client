<?php

namespace App\Console\Commands;

use App\Repositories\QuickTellerAPIInterface;
use Illuminate\Console\Command;

class FetchAPIData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:api-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all QuickTeller API data';

    /**
     * @var QuickTellerAPIInterface
     */
    private $apiService;

    /**
     * Create a new command instance.
     *
     * @param QuickTellerAPIInterface $apiService
     */
    public function __construct(QuickTellerAPIInterface $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Fetching Categories 🚀");
        $categories = $this->apiService->getCategories()['categorys'];

        $this->info("Fetching Billers 🚀");
        $billers = $this->apiService->getBillers()['billers'];

        $this->info("Combining categories and billers 🚀");
        $categoriesAndBillers = $this->combineCategoriesAndBillers($categories, $billers);

        $this->info("Fetch payment items 🚀");
        $paymentItems = $this->addBillerItems($categoriesAndBillers);
        dd($paymentItems);
    }

    /**
     * Combines categories and the billers
     * @param $categories
     * @param $billers
     * @return mixed
     */
    public function combineCategoriesAndBillers($categories, $billers)
    {
        return $categories->map(function ($category) use ($billers) {
            $categoryBillers = collect($billers)->filter(function ($biller) use ($category) {
                return collect($category['billerId'])->contains(function ($id) use ($biller) {
                    return $id === $biller['billerid'];
                });
            });
            $category['billers'] = $categoryBillers;
            return $category;
        });
    }

    public function addBillerItems($categoriesAndBillers)
    {
        return $categoriesAndBillers->map(function ($category) {
            $billers = $category['billers']->map(function ($biller) use ($category) {
                $biller['paymentitems'] = $this->apiService->getPaymentItems($biller['billerid'])['paymentitems'];
                return $biller;
            });
        });
    }
}
