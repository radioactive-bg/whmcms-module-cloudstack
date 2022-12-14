<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
include_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
use WHMCS\Module\Servers\cloudstack2\CloudstackInfo;
use WHMCS\Module\Servers\cloudstack2\CloudstackProvisioner;
use WHMCS\Database\Capsule;


function cloudstack2_MetaData() {
    return array(
        'DisplayName' => 'cloudstack2',
        'APIVersion' => '1.1', 
        'RequiresServer' => false, 
    );
}
function cloudstack2_LoadTemplates() { 
    $cloudstackInfo = new CloudstackInfo();
    $req = $cloudstackInfo->ListTemplates();
    $list = [];
    foreach ($req['listtemplatesresponse'] as $template) {
        foreach ($template as $i => $item) {
                $list[$item['id']] = ucfirst($item['name']);
        }  
    }
    return $list;
}
function cloudstack2_LoadServiceOfferings() { 
    $cloudstackInfo = new CloudstackInfo();
    $req = $cloudstackInfo->ListServiceOfferings();
    $list = [];
    foreach ($req['listserviceofferingsresponse'] as $serviceOffering) {
        foreach ($serviceOffering as $i => $item) {
                $list[$item['id']] = ucfirst($item['name'] . '| CPU:' . $item['cpunumber'] . '| RAM:' . $item['memory'] . '| Disk: ' . $item['rootdisksize']);
        }  
    }
    return $list;
}
function cloudstack2_LoadNetworkOfferings() {  
    $cloudstackInfo = new CloudstackInfo();
    $req = $cloudstackInfo->ListNetworkOfferings();
    $list = [];
    foreach ($req['listnetworkofferingsresponse'] as $networkOffering) {
        foreach ($networkOffering as $i => $item) {
                $list[$item['id']] = ucfirst($item['name']);
        }  
    }
    return $list;
}
function cloudstack2_LoadZones() {  
    $cloudstackInfo = new CloudstackInfo();
    $req = $cloudstackInfo->ListZones();
    $list = [];
    foreach ($req['listzonesresponse'] as $zones) {
        foreach ($zones as $i => $item) {
                $list[$item['id']] = ucfirst($item['name']);
        }  
    }
    return $list;
}
function cloudstack2_ConfigOptions() {
    try {
        if (!Capsule::schema()->hasTable('mod_cloudstack2')) {
            Capsule::schema()->create('mod_cloudstack2', function ($table) {
                $table->increments('id');
                $table->string('serviceId');
                $table->string('serverId');
                $table->string('networkId');
                $table->string('accountId');
                $table->string('ipAddress');
                $table->string('ipAddressId');
                $table->string('portforwardUDPId');
                $table->string('portforwardTCPId');
                $table->string('egressFirewallTCPId');
                $table->string('egressFirewallUDPId');
                $table->string('egressFirewallICMPId');
                $table->string('firewallTCPId');
                $table->string('firewallUDPId');
                $table->string('firewallICMPId');
                $table->string('templateId');
                $table->string('sshKeyId');
                $table->string('vmInitialPassword');
            });
        }
        return array(
            'Instance Prefix' => array(
                'Type' => 'text',
                'Size' => '10',
                'Default' => 'whmcs_',
                'SimpleMode' => true,
                'Description' => 'All instances will be created with this prefix',
            ),
            'ServiceOffering ID' => array(
                'Type' => 'text',
                'Size' => '40',
                'Loader' => 'cloudstack2_LoadServiceOfferings',
                'SimpleMode' => true,
            ),
            'NetworkOffering ID' => array(
                'Type' => 'text',
                'Size' => '40',
                'Loader' => 'cloudstack2_LoadNetworkOfferings',
                'SimpleMode' => true,
            ),
            'Zone ID' => array(
                'Type' => 'text',
                'Size' => '40',
                'Loader' => 'cloudstack2_LoadZones',
                'SimpleMode' => true,
            ),
            'Provisioning Template' => array(
                'FriendlyName' => "Provisioning Template",
                'Type' => 'textarea',
                'Rows' => '3',
                'Cols' => '50',
                'Default' => "",
                'Description' => 'Enter base64 encoded cloud-init provisioning template here',
                'SimpleMode' => true,
            ),
            
        );  
    } catch (Exception $e) {
        throw new \Exception($e->getMessage());
    }

}
function ProvisionIngressFirewall($serviceid,$ipaddressid) { 
    $cloudstackProvisioner = new CloudstackProvisioner();
    $numAttempts = 10;
    $curAttempts = 0;

    Capsule::table('mod_cloudstack2')->updateOrInsert(
        ['serviceId' => $serviceid],
        [
            'firewallUDPId' => $firewallUDP['createfirewallruleresponse']['id'],
            'firewallTCPId' => $firewallTCP['createfirewallruleresponse']['id'],
            'firewallICMPId' => $firewallICMP['createfirewallruleresponse']['id'],
        ]
        );
    return true;
}
function WaitForPassword($jobId) { 
    $cloudstackProvisioner = new CloudstackProvisioner();
    $numAttempts = 20;
    $curAttempts = 0;
    logModuleCall('provisioningmodule',__FUNCTION__,$params,$jobId,$curAttempts);
    do {
        try {
            $password = $cloudstackProvisioner->QueryAsyncJob($jobId);
            Capsule::table('mod_cloudstack2')->updateOrInsert(
                    ['serviceId' => $params['serviceid']],
                    [
                        'vmInitialPassword' => $password['queryasyncjobresultresponse']['jobresult']['virtualmachine']['password'],
                    ]
                    );
            Capsule::table('tblhosting')->updateOrInsert(
                        ['id' => $params['serviceid']],
                        [
                            'password' => $password['queryasyncjobresultresponse']['jobresult']['virtualmachine']['password'],
                        ]
                        );
            
            logModuleCall('provisioningmodule',__FUNCTION__,$params,$password,$password);
        } catch (Exception $e) {
            $curAttempts++;
            sleep(20);
            continue;
        }
        break;
    } while($curAttempts < $numAttempts);
    
}

function cloudstack2_CreateAccount(array $params) {
    try {
       $cloudstackProvisioner = new CloudstackProvisioner();
       $server_stat = Capsule::table('mod_cloudstack2')->where('serviceId', $params['serviceid'])->where('accountId' ,$params['accountid'])->first(); 
       if(is_null($server_stat->networkId)){
        $resp = $cloudstackProvisioner->ProvisionNewNetwork($params['clientsdetails']['uuid'],$params['configoption1'],$params['serviceid'], $params['configoption3'], $params['configoption4']);
        $associateIpAddress = $cloudstackProvisioner->ProvisionNewIP($resp['createnetworkresponse']['network']['id']);
        if(isset($associateIpAddress['associateipaddressresponse']['jobid'])) {
            $retry = 2;
            $retry_c = 0;
            do {
                $job_status = $cloudstackProvisioner->QueryAsyncJob($associateIpAddress['associateipaddressresponse']['jobid']);
                if(isset($job_status['queryasyncjobresultresponse']['jobresult']['ipaddress'])) {
                    $ipAddress = $cloudstackProvisioner->ListPublicIpAddressesById($associateIpAddress['associateipaddressresponse']['id']);
                    break;
                }
                sleep(10);
                $retry_c++;
            } while ($retry_c < $retry);
        }
        $egressFirewallTCP = $cloudstackProvisioner->ProvisionEgressFirewall($resp['createnetworkresponse']['network']['id'], 'TCP');
        $egressFirewallUDP = $cloudstackProvisioner->ProvisionEgressFirewall($resp['createnetworkresponse']['network']['id'], 'UDP');
        $egressFirewallICMP = $cloudstackProvisioner->ProvisionEgressFirewall($resp['createnetworkresponse']['network']['id'], 'ICMP');
        $firewallUDP = $cloudstackProvisioner->ProvisionUDPFirewall($associateIpAddress['associateipaddressresponse']['id']);
        $firewallTCP = $cloudstackProvisioner->ProvisionTCPFirewall($associateIpAddress['associateipaddressresponse']['id']);
        $firewallICMP = $cloudstackProvisioner->ProvisionICMPFirewall($associateIpAddress['associateipaddressresponse']['id']);
            Capsule::table('mod_cloudstack2')->updateOrInsert(
                ['serviceId' => $params['serviceid']],
                [
                    'accountId' => $params['accountid'],
                    'networkId' => $resp['createnetworkresponse']['network']['id'],
                    'ipAddress' => $ipAddress['listpublicipaddressesresponse']['publicipaddress'][0]['ipaddress'],
                    'ipAddressId' => $associateIpAddress['associateipaddressresponse']['id'],
                    'egressFirewallTCPId' => $egressFirewallTCP['createegressfirewallruleresponse']['id'],
                    'egressFirewallUDPId' => $egressFirewallUDP['createegressfirewallruleresponse']['id'],
                    'egressFirewallICMPId' => $egressFirewallICMP['createegressfirewallruleresponse']['id'],
                    'firewallUDPId' => $firewallUDP['createfirewallruleresponse']['id'],
                    'firewallTCPId' => $firewallTCP['createfirewallruleresponse']['id'],
                    'firewallICMPId' => $firewallICMP['createfirewallruleresponse']['id'],
                ]
                );
            Capsule::table('tblhosting')->updateOrInsert(
                ['id' => $params['serviceid']],
                [
                    'username' => 'ubuntu',
                    'dedicatedip' => $ipAddress['listpublicipaddressesresponse']['publicipaddress'][0]['ipaddress'],
                ]
                );
       }
       logModuleCall('provisioningmodule',__FUNCTION__,$params,$params['customfields']['sshKey'],$params['customfields']['sshKey']);

       if(isset($params['customfields']['sshKey'])){
            try {
                $sshKey = $cloudstackProvisioner->ProvisionNewSSHKeyPair($params['clientsdetails']['uuid'],$params['serviceid'],$params['configoption1'],$params['accountid'],$params['customfields']['sshKey']);
                logModuleCall('provisioningmodule',__FUNCTION__,$sshKey,$sshKey,$sshKey);
                Capsule::table('mod_cloudstack2')->updateOrInsert(
                    ['serviceId' => $params['serviceid']],
                    [
                        'sshKeyId' => $sshKey['registersshkeypairresponse']['keypair']['name'],
                    ]
                );
            } catch (Exception $e) {
                $fingerprint = violuke\RsaSshKeyFingerprint\FingerprintGenerator::getFingerprint($params['customfields']['sshKey']);
                $keyId = $cloudstackInfo->ListSSHKeyPairs($fingerprint);
                Capsule::table('mod_cloudstack2')->updateOrInsert(
                    ['serviceId' => $params['serviceid']],
                    [
                        'sshKeyId' => $keyId['listsshkeypairsresponse']['sshkeypair'][0]['name'],
                    ]
                );
            }
        }
    
       $updated_stat = Capsule::table('mod_cloudstack2')->where('serviceId', $params['serviceid'])->where('accountId' ,$params['accountid'])->first();
       if($updated_stat->serverId == "") {
        $newVM = $cloudstackProvisioner->ProvisionNewVirtualMachine($params['configoption5'],$params['clientsdetails']['uuid'],$params['configoption1'],$params['serviceid'],$params['configoptions']['Template'],$params['configoption4'],$updated_stat->networkId, $updated_stat->ipAddressId,$params['configoption2'],$updated_stat->sshKeyId);
        $portForwardingTCP = $cloudstackProvisioner->ProvisionPortForwardingRule($updated_stat->ipAddressId, $newVM['deployvirtualmachineresponse']['id'], 'TCP');
        $portForwardingUDP = $cloudstackProvisioner->ProvisionPortForwardingRule($updated_stat->ipAddressId, $newVM['deployvirtualmachineresponse']['id'], 'UDP');
        logModuleCall('provisioningmodule',__FUNCTION__,$params,$newVM,$newVM);
        
        Capsule::table('mod_cloudstack2')->updateOrInsert(
            ['serviceId' => $params['serviceid']],
            [
                'serverId' => $newVM['deployvirtualmachineresponse']['id'],
                'templateId' => $params['configoptions']['Template'],
                'vmInitialPassword' => $newVM['deployvirtualmachineresponse']['password'],
                'portforwardTCPId' => $portForwardingTCP['createportforwardingruleresponse']['id'],
                'portforwardUDPId' => $portForwardingUDP['createportforwardingruleresponse']['id'],
            ]
            );
            if($newVM['deployvirtualmachineresponse']['jobid'] != "") {
                logModuleCall('provisioningmodule',__FUNCTION__,$params,$deploy_job_status,$deploy_job_status);
                $retry = 15;
                $retry_c = 0;
                do {
                    $deploy_job_status = $cloudstackProvisioner->QueryAsyncJob($newVM['deployvirtualmachineresponse']['jobid']);
                    if($deploy_job_status['queryasyncjobresultresponse']['jobresult'] != "") {
                        logModuleCall('cl2cls2',__FUNCTION__,$params,$deploy_job_status,$deploy_job_status['queryasyncjobresultresponse']['jobresult']['virtualmachine']['password']);
                        $command = 'EncryptPassword';
                        $postData = array(
                            'password2' => $deploy_job_status['queryasyncjobresultresponse']['jobresult']['virtualmachine']['password'],
                        );
                        $results = localAPI($command, $postData);
                        Capsule::table('tblhosting')->updateOrInsert(
                            ['id' => $params['serviceid']],
                            [
                                'username' => 'ubuntu',
                                'password' => $results['password'],
                            ]
                            );

                            logModuleCall('cl2cls2',__FUNCTION__,$params,$results,$results);
                            Capsule::table('mod_cloudstack2')->updateOrInsert(
                                ['serviceId' => $params['serviceid']],
                                [
                                    'vmInitialPassword' => $deploy_job_status['queryasyncjobresultresponse']['jobresult']['virtualmachine']['password'],
                                ]
                                );
                    }
                    sleep(15);
                    $retry_c++;
                } while ($retry_c < $retry);
            }

       } else {
        logModuleCall('provisioningmodule',__FUNCTION__,$params,$updated_stat,$updated_stat->serverId);
       }

    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }
    return 'success';
}
function cloudstack2_SuspendAccount(array $params) {
    try {
        $cloudstackProvisioner = new CloudstackProvisioner();
        $server_stat = Capsule::table('mod_cloudstack2')->where('serviceId', $params['serviceid'])->where('accountId' ,$params['accountid'])->first();
        if($server_stat->portforwardTCPId != "" ){
            $cloudstackProvisioner->DeletePortForwardingRule($server_stat->portforwardTCPId);
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['portforwardTCPId' => "",]);
        }
        if($server_stat->portforwardUDPId != "" ){
            $cloudstackProvisioner->DeletePortForwardingRule($server_stat->portforwardUDPId);
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['portforwardUDPId' => "",]);
        }
        if($server_stat->egressFirewallTCPId != "" ){
            $cloudstackProvisioner->DeleteEgressFirewallRule($server_stat->egressFirewallTCPId);
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['egressFirewallTCPId' => "",]);
        }
        if($server_stat->egressFirewallUDPId != "" ){
            $cloudstackProvisioner->DeleteEgressFirewallRule($server_stat->egressFirewallUDPId);
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['egressFirewallUDPId' => "",]);
        }
        if($server_stat->egressFirewallICMPId != "" ){
            $cloudstackProvisioner->DeleteEgressFirewallRule($server_stat->egressFirewallICMPId);
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['egressFirewallICMPId' => "",]);
        }
        if($server_stat->firewallICMPId != "" ){
            $cloudstackProvisioner->DeleteFirewallRule($server_stat->firewallICMPId);
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['firewallICMPId' => "",]);
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}
function cloudstack2_UnsuspendAccount(array $params) {
    try {
        $cloudstackProvisioner = new CloudstackProvisioner();
        $server_stat = Capsule::table('mod_cloudstack2')->where('serviceId', $params['serviceid'])->where('accountId' ,$params['accountid'])->first();

        if($server_stat->egressFirewallTCPId == "") {
            $egressFirewallTCP = $cloudstackProvisioner->ProvisionEgressFirewall($server_stat->networkId,'TCP');
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['egressFirewallTCPId' => $egressFirewallTCP['createegressfirewallruleresponse']['id'],]);
        }
        if($server_stat->egressFirewallUDPId == "") {
            $egressFirewallUDP = $cloudstackProvisioner->ProvisionEgressFirewall($server_stat->networkId,'UDP');
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['egressFirewallUDPId' => $egressFirewallUDP['createegressfirewallruleresponse']['id'],]);
        }
        if($server_stat->egressFirewallICMPId == ""){
            $egressFirewallICMP = $cloudstackProvisioner->ProvisionEgressFirewall($server_stat->networkId,'ICMP');
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['egressFirewallICMPId' => $egressFirewallICMP['createegressfirewallruleresponse']['id'],]);
        }
        if($server_stat->portforwardTCPId == ""){
            $portForwardingTCP = $cloudstackProvisioner->ProvisionPortForwardingRule($server_stat->ipAddressId,$server_stat->serverId, 'TCP');
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['portforwardTCPId' => $portForwardingTCP['createportforwardingruleresponse']['id'],]);
        }
        if($server_stat->portforwardUDPId == ""){
            $portForwardingUDP = $cloudstackProvisioner->ProvisionPortForwardingRule($server_stat->ipAddressId,$server_stat->serverId, 'UDP');
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['portforwardUDPId' => $portForwardingUDP['createportforwardingruleresponse']['id'],]);
        }
        if($server_stat->firewallICMPid == ""){
            $firewallICMP = $cloudstackProvisioner->ProvisionICMPFirewall($server_stat->ipAddressId);
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['firewallICMPid' => $firewallICMP['createfirewallruleresponse']['id'],]);
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall('provisioningmodule',__FUNCTION__,$params,$e->getMessage(),$e->getTraceAsString());
        return $e->getMessage();
    }

    return 'success';
}
function cloudstack2_TerminateAccount(array $params){
    try {

        $cloudstackProvisioner = new CloudstackProvisioner();
        $server_status = Capsule::table('mod_cloudstack2')->where('serviceId', $params['serviceid'])->where('accountId' ,$params['accountid'])->first(); 
        if ($server_status->serverId != "") {
            $destroyVmResponse = $cloudstackProvisioner->DeleteVirtualMachine($server_status->serverId);
            logModuleCall('provisioningmodule',__FUNCTION__,$params,$destroyVmResponse,$destroyVmResponse);
            Capsule::table('mod_cloudstack2')->updateOrInsert(['serviceId' => $params['serviceid']],['serverId' => "",]);
        }
        
        if(isset($destroyVmResponse['destroyvirtualmachineresponse']['jobid'])){
            $retry = 10;
            $retry_c = 0;
            do {
               $job = $cloudstackProvisioner->QueryAsyncJob($destroyVmResponse['destroyvirtualmachineresponse']['jobid']);
               logModuleCall('provisioningmodule',__FUNCTION__,$params,$job,$job);
               if(isset($job['queryasyncjobresultresponse']['jobresult']['null'])) {
                break;
               }
               sleep(10);
               $retry_c++;
            } while($retry_c < $retry);
        }
        if($server_status->sshKeyId != "" ) {
            try {
                $destroyKeyResponse = $cloudstackProvisioner->DeleteSSHKeyPair($server_status->sshKeyId);
            } catch(Exception $e){ 
                logModuleCall('provisioningmodule',__FUNCTION__,$params,$e->getMessage(),$e->getTraceAsString());
            }
        }
        if($server_status->networkId != "" ) {
            $resp = $cloudstackProvisioner->DeleteNetwork($server_status->networkId);
            if(isset($resp['deletenetworkresponse']['jobid'])) {
                Capsule::table('mod_cloudstack2')->where('serviceId', $params['serviceid'])->where('accountId' ,$params['accountid'])->delete();
                Capsule::table('tblhosting')->updateOrInsert(
                    ['id' => $params['serviceid']],
                    [
                        'username' => 'ubuntu',
                        'dedicatedip' => "",
                    ]
                );
            } 
        }

         
    } catch (Exception $e) {
        logModuleCall('provisioningmodule',__FUNCTION__,$params,$e->getMessage(),$e->getTraceAsString());
        return $e->getMessage();
    }

    return 'success';
}
function cloudstack2_ChangePassword(array $params){
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'password' => 'The new service password',
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}
function cloudstack2_ChangePackage(array $params){
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'configoption1' => 'The new service disk space',
        //     'configoption3' => 'Whether or not to enable FTP',
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}
function cloudstack2_TestConnection(array $params){
    try {
        // Call the service's connection test function.

        $success = true;
        $errorMsg = '';
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        $success = false;
        $errorMsg = $e->getMessage();
    }

    return array(
        'success' => $success,
        'error' => $errorMsg,
    );
}
function cloudstack2_AdminCustomButtonArray()
{
    return array(
        "Reboot server"     => "rebootVmFunction",
        "Shutdown server"   => "shutdownServerFunction",
        "Start server"      => "startVMFunction",
    );
}
function cloudstack2_ClientAreaCustomButtonArray()
{
    return array(
        "Action 1 Display Value" => "actionOneFunction",
        "Action 2 Display Value" => "actionTwoFunction",
    );
}
function cloudstack2_rebootVmFunction(array $params)
{
    try {
        $cloudstackProvisioner = new CloudstackProvisioner();
        $server_stat = Capsule::table('mod_cloudstack2')->where('serviceId', $params['serviceid'])->where('accountId' ,$params['accountid'])->first();
        $cloudstackProvisioner->RebootVirtualMachine($server_stat->serverId);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}
function cloudstack2_shutdownVmFunction(array $params)
{
    try {
        $cloudstackProvisioner = new CloudstackProvisioner();
        $server_stat = Capsule::table('mod_cloudstack2')->where('serviceId', $params['serviceid'])->where('accountId' ,$params['accountid'])->first();
        $cloudstackProvisioner->ShutdownVirtualMachine($server_stat->serverId);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}
function cloudstack2_startVMFunction(array $params)
{
    try {
        $cloudstackProvisioner = new CloudstackProvisioner();
        $server_stat = Capsule::table('mod_cloudstack2')->where('serviceId', $params['serviceid'])->where('accountId' ,$params['accountid'])->first();
        $cloudstackProvisioner->StartVirtualMachine($server_stat->serverId);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}
function cloudstack2_actionOneFunction(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function cloudstack2_AdminServicesTabFields(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
        $response = array();
        //logModuleCall('provisioningmodule',__FUNCTION__,$params,$e->getMessage(),$e->getTraceAsString());
        $server_stat = Capsule::table('mod_cloudstack2')->where('serviceId', $params['serviceid'])->where('accountId' ,$params['accountid'])->first();
        // Return an array based on the function's response.
        return array(
            'Server ID' => $server_stat->serverId,
            'Network ID' => $server_stat->networkId,
            'ICMP Firewall ID' => $server_stat->firewallICMPId,
            'ServerPassword' => $server_stat->vmInitialPassword,
            'Last Access Date' => date("Y-m-d H:i:s", $response['lastLoginTimestamp']),
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, simply return no additional fields to display.
    }

    return array();
}

/**
 * Execute actions upon save of an instance of a product/service.
 *
 * Use to perform any required actions upon the submission of the admin area
 * product management form.
 *
 * It can also be used in conjunction with the AdminServicesTabFields function
 * to handle values submitted in any custom fields which is demonstrated here.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see cloudstack2_AdminServicesTabFields()
 */
function cloudstack2_AdminServicesTabFieldsSave(array $params)
{
    // Fetch form submission variables.
    $originalFieldValue = isset($_REQUEST['cloudstack2_original_uniquefieldname'])
        ? $_REQUEST['cloudstack2_original_uniquefieldname']
        : '';

    $newFieldValue = isset($_REQUEST['cloudstack2_uniquefieldname'])
        ? $_REQUEST['cloudstack2_uniquefieldname']
        : '';

    // Look for a change in value to avoid making unnecessary service calls.
    if ($originalFieldValue != $newFieldValue) {
        try {
            // Call the service's function, using the values provided by WHMCS
            // in `$params`.
        } catch (Exception $e) {
            // Record the error in WHMCS's module log.
            logModuleCall(
                'provisioningmodule',
                __FUNCTION__,
                $params,
                $e->getMessage(),
                $e->getTraceAsString()
            );

            // Otherwise, error conditions are not supported in this operation.
        }
    }
}

/**
 * Perform single sign-on for a given instance of a product/service.
 *
 * Called when single sign-on is requested for an instance of a product/service.
 *
 * When successful, returns a URL to which the user should be redirected.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function cloudstack2_ServiceSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on token retrieval function, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Perform single sign-on for a server.
 *
 * Called when single sign-on is requested for a server assigned to the module.
 *
 * This differs from ServiceSingleSignOn in that it relates to a server
 * instance within the admin area, as opposed to a single client instance of a
 * product/service.
 *
 * When successful, returns a URL to which the user should be redirected to.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function cloudstack2_AdminSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on admin token retrieval function,
        // using the values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Client area output logic handling.
 *
 * This function is used to define module specific client area output. It should
 * return an array consisting of a template file and optional additional
 * template variables to make available to that template.
 *
 * The template file you return can be one of two types:
 *
 * * tabOverviewModuleOutputTemplate - The output of the template provided here
 *   will be displayed as part of the default product/service client area
 *   product overview page.
 *
 * * tabOverviewReplacementTemplate - Alternatively using this option allows you
 *   to entirely take control of the product/service overview page within the
 *   client area.
 *
 * Whichever option you choose, extra template variables are defined in the same
 * way. This demonstrates the use of the full replacement.
 *
 * Please Note: Using tabOverviewReplacementTemplate means you should display
 * the standard information such as pricing and billing details in your custom
 * template or they will not be visible to the end user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function cloudstack2_ClientArea(array $params)
{
    // Determine the requested action and set service call parameters based on
    // the action.
    $requestedAction = isset($_REQUEST['customAction']) ? $_REQUEST['customAction'] : '';

    if ($requestedAction == 'manage') {
        $serviceAction = 'get_usage';
        $templateFile = 'templates/manage.tpl';
    } else {
        $serviceAction = 'get_stats';
        $templateFile = 'templates/overview.tpl';
    }

    try {
        // Call the service's function based on the request action, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        $extraVariable1 = 'abc';
        $extraVariable2 = '123';

        return array(
            'tabOverviewReplacementTemplate' => $templateFile,
            'templateVariables' => array(
                'extraVariable1' => $extraVariable1,
                'extraVariable2' => $extraVariable2,
            ),
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, display an error page.
        return array(
            'tabOverviewReplacementTemplate' => 'error.tpl',
            'templateVariables' => array(
                'usefulErrorHelper' => $e->getMessage(),
            ),
        );
    }
}
