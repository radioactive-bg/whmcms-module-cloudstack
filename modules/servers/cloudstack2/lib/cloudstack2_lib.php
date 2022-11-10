<?php
namespace WHMCS\Module\Servers\cloudstack2;
use PCextreme\Cloudstack\Client;


class CloudstackInfo {
    private function Client() { 
        $client = new Client([
            'urlApi'    => 'https://on.cloudhost360.net/client/api',
            'apiKey'    => "ux1Xdgo3ZXqB0uSkLlR1TQqErhQccz5_haVLlQqC6_jL4BePA4G2KT3NNKdlgpjF-IQZShy9rvObx2WFCFJryg",
            'secretKey' => "lWUsJCJ0yOHKw6DpoJb6hCfLQiVyuH1qA8OqHBYbmtgCupeoUf_mDd8jnzC-3XCCD5XJCYpc9yWl1Jf1qKcBnA",
        ]);
        return $client;
    }
    public function ListTemplates() {
        $client = $this->Client();
        return $client->listAccounts(['name' => 'admin', 'listall' => 'true']);
    }
}
