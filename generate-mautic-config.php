<?php

//VARIABLES
$configVars = array (
    'INSTANCE'                      => '', //this should indicate the client or client-project this app belongs to (e.g. ihub-f4c comms-compass)
    'NAMESPACE'                     => '', //this must be one of the four provisioned namespaces, but this is not enforced here.
    'CLUSTER_IP'                    => '', //this must be a number between 0-255 and be unique to this namespace (?maybe to the whole cluster?)
    'DB_CLUSTER_IP'                 => '', //this must be a number between 0-255 and be unique to this namespace, and differemt from CLUSTER_IP
    'DATABASE_PASSWORD'             => generateStrongPassword(32),  //currently the DB secret must be created manually from the values input here - due to character encoding blah blah.
    'DATABASE_USER'                 => 'user' . generateStrongPassword(5),
    'DATABASE_ROOT_PASSWORD'        => generateStrongPassword(32),
    'DATABASE_NAME'                 => 'mautic',
    'HASH_KEY'                      => '7bdc46b5a52f8725dd834dc8f125ff52452c9b08129553d6785aa5869383a9c5', //TODO: write function to generate this value.
    'MAILER_FROM_NAME'              => '', //This should be the name of the client org - it's overridable in the OCP4 admin.
    'MAILER_FROM_EMAIL'             => 'no-reply@gov.bc.ca', //The default is no-reply@gov.bc.ca
    'MAILER_ENCRYPTION'             => '', //default is null
    'MAILER_USER'                   => '', //in case you wish to use an external mailer with username/password etc.
    'MAILER_PORT'                   => '587', //Default 587; standard SMTP port.  
    'MAILER_AUTH_MODE'              => '', //TODO: enumerate possible values for auth mode. 
);
//TODO: There are additional parameters in the local.php configuration file - these should be included


$configFiles = array (
    'app/PersistentVolume-template.yaml' => $configVars['INSTANCE'] . '-PersistentVolume.yaml',
    'app/DeploymentConfig-template.yaml' => $configVars['INSTANCE'] . '-DeploymentConfig.yaml',
    'app/Service-template.yaml'          => $configVars['INSTANCE'] . '-Service.yaml',
    'app/Route-template.yaml'            => $configVars['INSTANCE'] . '-Route.yaml',
    'db/PersistentVolume-template.yaml'  => $configVars['INSTANCE'] . '-db-PersistentVolume.yaml',
    'db/DeploymentConfig-template.yaml'  => $configVars['INSTANCE'] . '-db-DeploymentConfig.yaml',
    'db/Service-template.yaml'           => $configVars['INSTANCE'] . '-db-Service.yaml',
    'db/Secret-template.yaml'            => $configVars['INSTANCE'] . '-db-Secret.yaml',
);
//TODO : Have system ingest a directory and recursively pull all the -template.yaml files.

//Simple Alias for now, may require pre-/post- steps in future.
function getFileAsString(string $path): string {
    return file_get_contents($path);
}

//Simple alias for now, may require pre-/post- steps in future.
function putStringAsFile(string $path, string $string) {
    return file_put_contents($path, $string);
}

//Variables were formatted to ENV var standards in the yaml files - wrapping them in curly braces.
function replaceVariableInString(string $configKey, string $configVal, string $configString): string {
    if (strlen($configKey) < 0) {
        throw new \LengthException('Config variable name cannot be empty.');
    }
    if (strlen($configVal) < 0 ){
        throw new \LengthException('Config value cannot be empty');
    }
    $configKeyPresentation = '${' . $configKey . '}';
    return str_replace($configKeyPresentation, $configVal, $configString);
}

//Generates an actual random password.
function generateStrongPassword(
    int $length = 24, 
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): 
    string {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
}

//Iterate over each of the files, replace variables with values. 
foreach ($configFiles as $configFileTemplate => $configFileOutput) :
    $configString = getFileAsString($configFileTemplate);
    foreach ($configVars as $configVarKey => $configVarVal) :
        $configString = replaceVariableInString($configVarKey, $configVarVal, $configString);
    endforeach;    
    putStringAsFile($configFileOutput, $configString);
endforeach;



//TODO: Generate a .sh file to execute oc apply against all generated scripts