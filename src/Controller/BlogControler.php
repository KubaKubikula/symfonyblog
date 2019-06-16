<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogControler extends AbstractController
{

    private $goods;
    private $firstCustomerHistory;
    private $secondCustomerHistory;
    private $thirdCustomerHistory;

    private function getData(){

        $this->goods = array(
            1 => 180, // Biofinity (6 lenses)
            2 => 90, // Biofinity (3 lenses)
            3 => 30, // Focus Dailies (30)
        );

        $this->firstCustomerHistory = array(
                '2015-04-01' => array(
                array(1, 2, '-2.00'),
                array(1, 2, '-3.00'),
            ),
        );

        $this->thirdCustomerHistory = array(
            '2014-08-01' => array(
                array(2, 2, '+0.50'),
            ),
        );

        $this->secondCustomerHistory = array(
            '2014-10-01' => array(
                array(3, 2, '-1.50'),
                array(3, 2, '-3.50'),
            ),
            '2015-01-01' => array(
                array(3, 2, '-1.50'),
                array(3, 2, '-3.50'),
            ),
            '2015-04-15' => array(
                array(3, 1, '-1.50'),
                array(3, 1, '-3.50'),
            ),
        );



    }


    public function calculateAverageUsageAndLastContactsOut($customerHistoryOrders, $goods) : \DateTime {
        $lensesLastsOrders = array();
        $average = 1;

        foreach($customerHistoryOrders as $dateOrder => $values) {

            $countAllGoodsDays = array();
            $dateOrder = new \DateTime($dateOrder);

            // vytvořím pole pro každé oko s tím kolik to vydrží dní ty čočky
            foreach($values as $lensItem) {

                if(!isset($countAllGoodsDays[$lensItem[2]])) {
                    $countAllGoodsDays[$lensItem[2]] = 0;
                }

                $countAllGoodsDays[$lensItem[2]] += $goods[$lensItem[0]] * $lensItem[1];
            }

            // spočítam kolik to realně bylo odečtením od data poslední objednávky
            if(isset($lastDateOrder) AND isset($dayLensesGone)) {

                $realDays = $dateOrder->diff($lastDateOrder)->format("%a");

                // vytvořím konstantu poměru relaných dní nošení a doporučených dní nošení
                $lensesLastsOrders[] = $realDays / $dayLensesGone;
            }

            $dayLensesGone = 0;
            // vypočtu nový počet dní nošení
            if(count($countAllGoodsDays) == 1) {

                // pokud koupí jedny tzn. že má obě oči stejné a tím padem mu vydrži polovinu doby
                $dayLensesGone = reset($countAllGoodsDays) / 2;
            } else {

                // pokud má rozdílné pak mu vydrži nejkratší dobu jednoho oka
                foreach ($countAllGoodsDays as $days) {
                    if(!$dayLensesGone) {
                        $dayLensesGone = $days;
                    } else {
                        if($dayLensesGone > $days) {
                            $dayLensesGone = $days;
                        }
                    }
                }
            }

            $lastDateOrder = $dateOrder;
        }

        // tady vymu všechny konstanty doby nošení a udělám průměr
        if(count($lensesLastsOrders)) {
            $average = array_sum($lensesLastsOrders) / count($lensesLastsOrders);
        }

        // pošlu datum kdy by mu měly dojít vynásobené a průměrnou konstantu délky nošení aby náš předpoklad byl podle reality
        return $lastDateOrder->add(new \DateInterval('P' . ($dayLensesGone * $average) . 'D'));
    }





    /**
     *
     * @Route("/")
     */
    public function list()
    {
        $this->getData();

        echo "secondCustomer";
        $dateExpire = self::calculateAverageUsageAndLastContactsOut($this->firstCustomerHistory, $this->goods);
        var_dump($dateExpire);
        die;


        return $this->render(
            'blog/list.html.twig'
        );
    }

    /**
     * @Route("/article/{slug}")
     */
    public function detail($slug)
    {
        return $this->render(
            'blog/detail.html.twig'
        );
    }
}