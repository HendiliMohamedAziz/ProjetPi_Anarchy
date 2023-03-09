<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Charge;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StripeService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        Stripe::setApiKey('pk_test_51MdLLSIdxzsb4CtasR40zMKLPQ8sJZ6Sspx1OB965IpLit6ldRRtempWVUPZDQz8Ct8VeRna3PnFDALzJ7b8aip700hR1H7C4n');
    }

    public function charge($amount, $token)
    {
        try {
            $charge = Charge::create([
                'amount' => $amount,
                'currency' => 'DT',
                'source' => $token,
            ]);
            return $charge;
        } catch (\Exception $e) {
            throw new \Exception('Error processing payment');
        }
    }
}
