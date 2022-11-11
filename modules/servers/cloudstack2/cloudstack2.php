<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
include_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
use WHMCS\Module\Servers\cloudstack2\CloudstackInfo;
use WHMCS\Module\Servers\cloudstack2\CloudstackProvisioner;
function cloudstack2_MetaData()
{
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

function cloudstack2_ConfigOptions()
{
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
    );
}

/**
 * Provision a new instance of a product/service.
 *
 * Attempt to provision a new instance of a given product/service. This is
 * called any time provisioning is requested inside of WHMCS. Depending upon the
 * configuration, this can be any of:
 * * When a new order is placed
 * * When an invoice for a new order is paid
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function cloudstack2_CreateAccount(array $params)
{
    try {
        // Call the service's provisioning function, using the values provided
        // by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'domain' => 'The domain of the service to provision',
        //     'username' => 'The username to access the new service',
        //     'password' => 'The password to access the new service',
        //     'configoption1' => 'The amount of disk space to provision',
        //     'configoption2' => 'The new services secret key',
        //     'configoption3' => 'Whether or not to enable FTP',
        //     ...
        // )
        // ```
       $cloudstackProvisioner = new CloudstackProvisioner();
       $dedicated_ip = $params['model']->serviceProperties->get('Dedicated IP');
       logModuleCall(
        'provisioningmodule',
        __FUNCTION__,
        $associateIpAddress,
        $dedicated_ip,
        $params);
   
       if(is_null($dedicated_ip)) {
        $resp = $cloudstackProvisioner->ProvisionNewNetwork($params['configoption2'], $params['configoption3'], $params['configoption4']);
        $associateIpAddress = $cloudstackProvisioner->ProvisionNewIP($resp['createnetworkresponse']['network']['id']);
        $ipAddress = $cloudstackProvisioner->ListPublicIpAddressesById($ipAddress['associateipaddressresponse']['id']);
        $params['model']->serviceProperties->save(['Dedicated IP' => $ipAddress['listpublicipaddressesresponse']['ipaddress']]);
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $associateIpAddress,
            $ipAddress,
            $resp['createnetworkresponse']['network']['id']);
       }
       

       logModuleCall(
        'provisioningmodule',
        __FUNCTION__,
        $ipAddress,
        $ipAddress,
        $resp['createnetworkresponse']['network']['id']
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

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Suspend an instance of a product/service.
 *
 * Called when a suspension is requested. This is invoked automatically by WHMCS
 * when a product becomes overdue on payment or can be called manually by admin
 * user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function cloudstack2_SuspendAccount(array $params)
{
    try {
        // Call the service's suspend function, using the values provided by
        // WHMCS in `$params`.
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

/**
 * Un-suspend instance of a product/service.
 *
 * Called when an un-suspension is requested. This is invoked
 * automatically upon payment of an overdue invoice for a product, or
 * can be called manually by admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function cloudstack2_UnsuspendAccount(array $params)
{
    try {
        // Call the service's unsuspend function, using the values provided by
        // WHMCS in `$params`.
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

/**
 * Terminate instance of a product/service.
 *
 * Called when a termination is requested. This can be invoked automatically for
 * overdue products if enabled, or requested manually by an admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function cloudstack2_TerminateAccount(array $params)
{
    try {
        // Call the service's terminate function, using the values provided by
        // WHMCS in `$params`.
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

/**
 * Change the password for an instance of a product/service.
 *
 * Called when a password change is requested. This can occur either due to a
 * client requesting it via the client area or an admin requesting it from the
 * admin side.
 *
 * This option is only available to client end users when the product is in an
 * active status.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function cloudstack2_ChangePassword(array $params)
{
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

/**
 * Upgrade or downgrade an instance of a product/service.
 *
 * Called to apply any change in product assignment or parameters. It
 * is called to provision upgrade or downgrade orders, as well as being
 * able to be invoked manually by an admin user.
 *
 * This same function is called for upgrades and downgrades of both
 * products and configurable options.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function cloudstack2_ChangePackage(array $params)
{
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

/**
 * Renew an instance of a product/service.
 *
 * Attempt to renew an existing instance of a given product/service. This is
 * called any time a product/service invoice has been paid. 
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function cloudstack2_Renew(array $params)
{
    try {
        // Call the service's provisioning function, using the values provided
        // by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'domain' => 'The domain of the service to provision',
        //     'username' => 'The username to access the new service',
        //     'password' => 'The password to access the new service',
        //     'configoption1' => 'The amount of disk space to provision',
        //     'configoption2' => 'The new services secret key',
        //     'configoption3' => 'Whether or not to enable FTP',
        //     ...
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

/**
 * Test connection with the given server parameters.
 *
 * Allows an admin user to verify that an API connection can be
 * successfully made with the given configuration parameters for a
 * server.
 *
 * When defined in a module, a Test Connection button will appear
 * alongside the Server Type dropdown when adding or editing an
 * existing server.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function cloudstack2_TestConnection(array $params)
{
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

/**
 * Additional actions an admin user can invoke.
 *
 * Define additional actions that an admin user can perform for an
 * instance of a product/service.
 *
 * @see cloudstack2_buttonOneFunction()
 *
 * @return array
 */
function cloudstack2_AdminCustomButtonArray()
{
    return array(
        "Button 1 Display Value" => "buttonOneFunction",
        "Button 2 Display Value" => "buttonTwoFunction",
    );
}

/**
 * Additional actions a client user can invoke.
 *
 * Define additional actions a client user can perform for an instance of a
 * product/service.
 *
 * Any actions you define here will be automatically displayed in the available
 * list of actions within the client area.
 *
 * @return array
 */
function cloudstack2_ClientAreaCustomButtonArray()
{
    return array(
        "Action 1 Display Value" => "actionOneFunction",
        "Action 2 Display Value" => "actionTwoFunction",
    );
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see cloudstack2_AdminCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function cloudstack2_buttonOneFunction(array $params)
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

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see cloudstack2_ClientAreaCustomButtonArray()
 *
 * @return string "success" or an error message
 */
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

/**
 * Admin services tab additional fields.
 *
 * Define additional rows and fields to be displayed in the admin area service
 * information and management page within the clients profile.
 *
 * Supports an unlimited number of additional field labels and content of any
 * type to output.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see cloudstack2_AdminServicesTabFieldsSave()
 *
 * @return array
 */
function cloudstack2_AdminServicesTabFields(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
        $response = array();

        // Return an array based on the function's response.
        return array(
            'Number of Apples' => (int) $response['numApples'],
            'Number of Oranges' => (int) $response['numOranges'],
            'Last Access Date' => date("Y-m-d H:i:s", $response['lastLoginTimestamp']),
            'Something Editable' => '<input type="hidden" name="cloudstack2_original_uniquefieldname" '
                . 'value="' . htmlspecialchars($response['textvalue']) . '" />'
                . '<input type="text" name="cloudstack2_uniquefieldname"'
                . 'value="' . htmlspecialchars($response['textvalue']) . '" />',
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
