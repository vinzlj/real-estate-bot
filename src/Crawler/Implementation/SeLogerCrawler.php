<?php

declare(strict_types=1);

namespace Crawler\Implementation;

use Crawler\AdCrawlerInterface;
use Crawler\BaseCrawler;
use Symfony\Component\DomCrawler\Crawler;

class SeLogerCrawler extends BaseCrawler implements AdCrawlerInterface
{
    public function extractAdId(Crawler $adCrawler): string
    {
        preg_match('/\/(\d{5,})\.htm/', $this->extractAdUrl($adCrawler), $matches);

        return $matches[1];
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        $selector = 'a[class*=\'CoveringLink-\']';

        preg_match('/(.*\.htm)/', $adCrawler->filter($selector)->attr('href'), $matches);

        return $matches[1];
    }

    protected function getRequestHeaders(): array
    {
        return [
            'sec-fetch-site' => 'same-origin',
            'sec-fetch-mode' => 'navigate',
            'sec-fetch-user' => '?1',
            'sec-fetch-dest' => 'document',
            'pragma' => 'no-cache',
            'cache-control' => 'no-cache',
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'accept-language' => 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36',
            'cookie' => '__uzma=bbe49b06-f720-5c44-7407-373cd847d438; __uzmb=1590668259; visitId=1590668264495-10431316; atuserid=%7B%22name%22%3A%22atuserid%22%2C%22val%22%3A%2269e05ae4-2c0e-4d05-83d9-4aeddc188776%22%2C%22options%22%3A%7B%22end%22%3A%222021-06-29T12%3A17%3A44.907Z%22%2C%22path%22%3A%22%2F%22%7D%7D; theshield_cmp_consent={%22consentString%22:%22eyJhdWRpZW5jZSI6WyIqIl0sInNvY2lhbCI6WyIqIl0sImFuYWx5dGljcyI6WyIqIl0sImlhYiI6W3siaWQiOjEsInZlbmRvcnMiOlsiKiJdfSx7ImlkIjoyLCJ2ZW5kb3JzIjpbIioiXX0seyJpZCI6MywidmVuZG9ycyI6WyIqIl19LHsiaWQiOjQsInZlbmRvcnMiOlsiKiJdfSx7ImlkIjo1LCJ2ZW5kb3JzIjpbIioiXX1dLCJhZHMiOlsiKiJdfQ%253D%253D%22}; theshield_consent={%22consentString%22:%22BO0HLclO0HLclCyABBFRDL-AAAAv17_______9______9uz_Ov_v_f__33e8__9v_l_7_-___u_-23d4u_1vf99yfm1-7etr3tp_87ues2_Xur__71__3z3_9pxP78k89r7335Ew_v-_v-b7BCPN9Y3v-8K9wA%22}; _gcl_au=1.1.1307557390.1590668266; bannerCookie=1; AMCVS_366134FA53DB27860A490D44%40AdobeOrg=1; s_ecid=MCMID%7C87354350029276995202444494984010357302; _ga=GA1.2.30109159.1590668267; s_visit=1; c_m=undefinedTyped%2FBookmarkedTyped%2FBookmarkedundefined; stack_ch=%5B%5B%27Acces%2520Direct%27%2C%271590668267475%27%5D%5D; s_cc=true; _hjid=4c181341-5a51-46e9-ac32-69bfdf47754d; ry_ry-s3oa268o_realytics=eyJpZCI6InJ5XzgzREFGODI3LUVBQ0QtNEM2NS04RTY0LTg1ODVBRTQyQTU5MSIsImNpZCI6bnVsbCwiZXhwIjoxNjIyMjA0MjY3MzE2LCJjcyI6MX0%3D; mics_uaid=web:1056:916a1205-abf8-4767-b1f3-fc819db137d6; uid=916a1205-abf8-4767-b1f3-fc819db137d6; mics_vid=7501220002; mics_lts=1590668268687; mics_vid=7501220002; mics_lts=1590668268687; __gads=ID=4c75d32e9585e647:T=1590668269:S=ALNI_MYpXGQO_3hq0wrKQB3RSbt-eHTmFw; AMCV_366134FA53DB27860A490D44%40AdobeOrg=1099438348%7CMCIDTS%7C18413%7CMCMID%7C87354350029276995202444494984010357302%7CMCAAMLH-1591273066%7C6%7CMCAAMB-1591442035%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1590844435s%7CNONE%7CMCAID%7CNONE%7CvVersion%7C2.1.0; _gid=GA1.2.1224033974.1590837237; s_dl=1; ry_ry-s3oa268o_so_realytics=eyJpZCI6InJ5XzgzREFGODI3LUVBQ0QtNEM2NS04RTY0LTg1ODVBRTQyQTU5MSIsImNpZCI6bnVsbCwib3JpZ2luIjp0cnVlLCJyZWYiOm51bGwsImNvbnQiOm51bGwsIm5zIjpmYWxzZX0%3D; realytics=1; kameleoonVisitorCode=_js_9ur1mxdz07kmp7ra; s_sq=%5B%5BB%5D%5D; _hjAbsoluteSessionInProgress=1; s_getNewRepeat=1590843295774-Repeat; _gat_UA-482515-1=1; _gat_UA-155862534-1=1; __uzmd=1590843300; __uzmc=6575416910632',
        ];
    }
}
