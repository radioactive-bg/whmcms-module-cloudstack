<?php
namespace WHMCS\Module\Servers\cloudstack2;
use PCextreme\Cloudstack\Client;

class CloudstackClient {
    protected function Client() { 
        $client = new Client([
            'urlApi'    => 'https://on.cloudhost360.net/client/api',
            'apiKey'    => "ux1Xdgo3ZXqB0uSkLlR1TQqErhQccz5_haVLlQqC6_jL4BePA4G2KT3NNKdlgpjF-IQZShy9rvObx2WFCFJryg",
            'secretKey' => "lWUsJCJ0yOHKw6DpoJb6hCfLQiVyuH1qA8OqHBYbmtgCupeoUf_mDd8jnzC-3XCCD5XJCYpc9yWl1Jf1qKcBnA",
        ]);
        return $client;
    }
    
}

class CloudstackInfo extends CloudstackClient {
    
    public function ListTemplates() {
        $client = parent::Client();
        return $client->listTemplates(['templatefilter' => 'self', 'listall' => 'true']);
    }
    
    public function ListServiceOfferings() { 
        $client = parent::Client();
        return $client->listServiceOfferings(['listall' => 'true']);
    }
    public function ListZones() {
        $client = parent::Client();
        return $client->listZones();
    }
    public function ListNetworkOfferings() {
        $client = parent::Client();
        return $client->listNetworkOfferings();
    }
    
}
class CloudstackProvisioner extends CloudstackClient {
    private function ProvisionNewNetwork($serviceid,$networkofferingid,$zoneid) { 
        $client = parent::Client();
        try {
            $resp = $client->createNetwork([
                'displaytext' => $serviceid . '_network',
                'name' => $serviceid . '_network',
                'networkofferingid' => $networkofferingid,
                'zoneid' => $zoneid
            ]);
        } catch (Exception $e) {
            logModuleCall(
                'provisioningmodule',
                __FUNCTION__,
                $params,
                $e->getMessage(),
                $e->getTraceAsString()
            );
            return $e->getMessage();
        }
        return $resp;

    }
    private function ProvisionNewIP($networkid) { 
        $client = parent::Client();
        try {
          $ipid =  $client->associateIpAddress([
                'networkid' => $networkid,
            ]);
        } catch (Exception $e) {
            logModuleCall(
                'provisioningmodule',
                __FUNCTION__,
                $params,
                $e->getMessage(),
                $e->getTraceAsString()
            );
            return $e->getMessage();
        }
        return $ipid;
    }
}