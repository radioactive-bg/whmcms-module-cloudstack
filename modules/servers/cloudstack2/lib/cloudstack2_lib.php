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
    public function ListNetworks() { 
        $client = parent::Client();
        return $client->listNetworks();
    }
    public function FindKeyByFingerprint($fingerprint) {
        $client = parent::Client();
        return $client->listSSHKeyPairs[(['fingerprint' => $fingerprint])];

    }

}
class CloudstackProvisioner extends CloudstackClient {
    public function ListPublicIpAddressesById($id) {
        $client = parent::Client();
        return $client->listPublicIpAddresses(['id' => $id]);
    }
    public function QueryAsyncJob($jobId){
        $client = parent::Client();
        return $client->queryAsyncJobResult(['jobid' => $jobId]);
    }
    public function ProvisionNewNetwork($prefix,$serviceid,$networkofferingid,$zoneid) { 
        $client = parent::Client();
        try {
            $resp = $client->createNetwork([
                'displaytext' => $prefix . '-' . $serviceid . '-network',
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
    public function ProvisionNewIP($networkid) { 
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
    public function ProvisionEgressFirewall($networkid,$proto) { 
        $client = parent::Client();
        try {
            $resp = $client->createEgressFirewallRule([
                'protocol' => $proto,
                'cidrlist' => '0.0.0.0/0',
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
        return $resp;
    }
    public function ProvisionTCPFirewall($ipaddressid) {
        $client = parent::Client();
        try {
            $resp = $client->createFirewallRule([
                'protocol' => 'tcp',
                'cidrlist' => '0.0.0.0/0',
                'startport' => '1',
                'endport' => '65535',
                'ipaddressid' => $ipaddressid,
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
    public function ProvisionUDPFirewall($ipaddressid) {
        $client = parent::Client();
        try {
            $resp = $client->createFirewallRule([
                'protocol' => 'udp',
                'cidrlist' => '0.0.0.0/0',
                'startport' => '1',
                'endport' => '65535',
                'ipaddressid' => $ipaddressid,
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
    public function ProvisionICMPFirewall($ipaddressid) {
        $client = parent::Client();
        try {
            $resp = $client->createFirewallRule([
                'protocol' => 'ICMP',
                'cidrlist' => '0.0.0.0/0',
                'ipaddressid' => $ipaddressid,
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
    public function ProvisionNewVirtualMachine($prefix, $serviceid,$templateid,$zoneid,$networkid,$ipaddressid,$serviceofferingid,$sshkeyid) {
        $client = parent::Client();
        try {
            $resp = $client->deployVirtualMachine([
                'displayname' => $prefix . '-' . $serviceid . '-vm',
                'name' => $prefix . '-' . $serviceid . '-vm',
                'templateid' => $templateid,
                'zoneid' => $zoneid,
                'networkids' => $networkid,
                'serviceofferingid' => $serviceofferingid,
                'keypair' => $keypair,
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
    public function ProvisionPortForwardingRule($ipaddressid,$virtualmachineid,$proto) {
        $client = parent::Client();
        try {
            $resp = $client->createPortForwardingRule([
                'ipaddressid' => $ipaddressid,
                'virtualmachineid' => $virtualmachineid,
                'protocol' => $proto,
                'publicport' => '1',
                'publicendport' => '65535',
                'privateport' => '1',
                'privateendport' => '65535',
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
    public function ProvisionNewSSHKeyPair($serviceid,$prefix,$accId,$sshpublickey) {
        $client = parent::Client();
        try {
            $resp = $client->registerSSHKeyPair([
                'name' => $prefix . '-' . $accId . '-' . $serviceid . '-keypair',
                'publickey' => $sshpublickey,

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
    public function ProvisionNewVolume($serviceid,$zoneid,$diskofferingid) {
        $client = parent::Client();
        try {
            $resp = $client->createVolume([
                'displayname' => $serviceid . '_volume',
                'name' => $serviceid . '_volume',
                'zoneid' => $zoneid,
                'diskofferingid' => $diskofferingid,
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
    public function ProvisionNewVolumeAttachment($serviceid,$volumeid,$virtualmachineid) {
        $client = parent::Client();
        try {
            $resp = $client->attachVolume([
                'id' => $volumeid,
                'virtualmachineid' => $virtualmachineid,
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
    public function DeleteVirtualMachine($id) {
        $client = parent::Client();
        try {
            $resp = $client->destroyVirtualMachine([
                'id' => $id,
                'expunge' => "true",
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
    public function DeleteFirewallRule($id) {
        $client = parent::Client();
        try {
            $resp = $client->deleteFirewallRule([
                'id' => $id,
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
    public function DeleteEgressFirewallRule($id) {
        $client = parent::Client();
        try {
            $resp = $client->deleteEgressFirewallRule([
                'id' => $id,
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
    public function DeletePortForwardingRule($ruleid){
        $client = parent::Client();
        try {
            $resp = $client->deletePortForwardingRule([
                'id' => $ruleid,
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
    public function DeleteVolumeAttachment($serviceid,$volumeid) {
        $client = parent::Client();
        try {
            $resp = $client->detachVolume([
                'id' => $volumeid,
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
    public function DeleteSSHKeyPair($keypair) {
        $client = parent::Client();
        try {
            $resp = $client->deleteSSHKeyPair([
                'name' => $keypair,
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
    public function DeleteNetwork($networkid) {
        $client = parent::Client();
        try {
            $resp = $client->deleteNetwork([
                'id' => $networkid,
                'forced' => "true",
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
}