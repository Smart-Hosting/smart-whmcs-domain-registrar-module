<?php

function smarthosting_getAPI($params)
{
	require_once(dirname(__FILE__) . "/API.class.php");
	return new \SmartHosting\API1\API($params["API_Username"], $params["API_Secret"]);
}

function smarthosting_getDomainId(&$API, $domain)
{
	return $API->call("services/domains/find", "GET", ["domain" => $domain])["payload"]["domain"]["id"];
}

function smarthosting_getConfigArray()
{
	return
	[		
		"FriendlyName" =>
		[
			"Type" => "System",
			"Value" => "Smart Hosting Ltd"
		],
		
		"API_Username" =>
		[
			"Type" => "text",
			"Size" => "20",
			"Description" => "Enter your API username here"
		],
		
		"API_Secret" =>
		[
			"Type" => "text",
			"Size" => "40",
			"Description" => "Enter your API secret here"
		],
		
		"Use_Credit_Card" =>
		[
			"Type" => "yesno",
			"Description" => "Tick to attempt to charge your CC for domain orders. If disabled, and you have no credit balance in your account, your orders will fail."
		],
	];
}

function smarthosting_GetNameservers($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		return $API->call("services/domains/" . smarthosting_getDomainId($API, $params["domainname"]) . "/nameservers", "GET")["payload"]["nameservers"];
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_SaveNameservers($params)
{	
	$API = smarthosting_getAPI($params);

	try
	{
		$API->call("services/domains/" . smarthosting_getDomainId($API, $params["domainname"]) . "/nameservers", "PUT", [], ["nameservers" => [$params["ns1"], $params["ns2"], $params["ns3"], $params["ns4"]]]);
		return ["error" => ""];
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_GetRegistrarLock($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		return ($API->call("services/domains/" . smarthosting_getDomainId($API, $params["domainname"]) . "/locking", "GET")["payload"]["locked"]) ? "locked" : "unlocked";
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_SaveRegistrarLock($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		$API->call("services/domains/" . smarthosting_getDomainId($API, $params["domainname"]) . "/locking", "PUT", [], ["locked" => (($params["lockenabled"] == "locked") ? true : false)]);
		return ["error" => ""];
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_ReleaseDomain($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		$API->call("services/domains/" . smarthosting_getDomainId($API, $params["domainname"]) . "/release", "POST", [], ["ips_tag" => $params["transfertag"]]);
		return ["error" => ""];
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_RegisterDomain($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		$API->call("services/domains/", "POST", ["use_cc" => (($params["Use_Credit_Card"] == "on") ? "1" : "")],
		[
			"domain" =>
			[
				"domain" => $params["domainname"],
				"term" => $params["regperiod"],
				"order_type" => "registration",
				"additional_fields" => $params["additionalfields"],
				"nameservers" => [$params["ns1"], $params["ns2"], $params["ns3"], $params["ns4"]],
				"id_protection" => (($params["idprotection"]) ? true : false) 
			],
			
			"contact_details" =>
			[
				"firstname" => $params["firstname"],
				"lastname" => $params["lastname"],
				"address1" => $params["address1"],
				"address2" => $params["address2"],
				"city" => $params["city"],
				"state" => $params["state"],
				"postcode" => $params["postcode"],
				"country" => $params["country"],
				"email" => $params["email"],
				"phonenumber" => $params["phonenumber"]
			]
		]);
		
		return ["error" => ""];
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_TransferDomain($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		$API->call("services/domains/", "POST", ["use_cc" => (($params["Use_Credit_Card"] == "on") ? "1" : "")],
		[
			"domain" =>
			[
				"domain" => $params["domainname"],
				"term" => $params["regperiod"],
				"order_type" => "transfer",
				"additional_fields" => $params["additionalfields"],
				"nameservers" => [$params["ns1"], $params["ns2"], $params["ns3"], $params["ns4"]],
				"epp_code" => $params["eppcode"],
				"id_protection" => (($params["idprotection"]) ? true : false) 
			],
			
			"contact_details" =>
			[
				"firstname" => $params["firstname"],
				"lastname" => $params["lastname"],
				"address1" => $params["address1"],
				"address2" => $params["address2"],
				"city" => $params["city"],
				"state" => $params["state"],
				"postcode" => $params["postcode"],
				"country" => $params["country"],
				"email" => $params["email"],
				"phonenumber" => $params["phonenumber"]
			]
		]);
		
		return ["error" => ""];
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_RenewDomain($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		$API->call("services/domains/" . smarthosting_getDomainId($API, $params["domainname"]) . "/renew", "POST", ["use_cc" => (($params["Use_Credit_Card"] == "on") ? "1" : "")], ["term" => $params["regperiod"]]);
		return ["error" => ""];
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_GetContactDetails($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		$contacts = $API->call("services/domains/" . smarthosting_getDomainId($API, $params["domainname"]) . "/contacts", "GET")["payload"]["contacts"];
		
		$newContacts = [];
		
		foreach($contacts as $contact => $contactSet)
		{
			if(!isset($newContacts[$contact])) $newContacts[$contact] = [];
			
			foreach($contactSet as $fieldName => $value)
			{
				$fieldName = str_replace("_", " ", $fieldName);
				$newContacts[$contact][$fieldName] = $value;
			}
		}
		
		return $newContacts;
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_SaveContactDetails($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		$API->call("services/domains/" . smarthosting_getDomainId($API, $params["domainname"]) . "/contacts", "PUT", [], ["contacts" => $params["contactdetails"]]);
		return ["error" => ""];
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_GetEPPCode($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		$payload = $API->call("services/domains/" . smarthosting_getDomainId($API, $params["domainname"]) . "/epp_code", "GET")["payload"];
		if($payload["emailed"]) return ["eppcode" => "", "error" => ""];
		return ["eppcode" => $payload["epp_code"], "error" => ""];
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_TransferSync($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		$domain = $API->call("services/domains/find", "GET", ["domain" => $params["domain"]])["payload"]["domain"];
		
		$values = [];
		
		// Add in the expiry if we have it
		if($domain["expirydate"]) $values["expirydate"] = $domain["expirydate"];
		
		if($domain["status"] == "active")
		{
			$values["completed"] = true;
		}
		elseif($domain["status"] != "pending transfer")
		{
			$values["failed"] = true;
			$values["reason"] = "Please contact us for more information";
		}
		
		return $values;
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function smarthosting_Sync($params)
{
	$API = smarthosting_getAPI($params);

	try
	{
		$domain = $API->call("services/domains/find", "GET", ["domain" => $params["domain"]])["payload"]["domain"];
		
		$values = [];
		
		// Add in the expiry if we have it
		$values["expirydate"] = $domain["expirydate"];
		
		if($domain["status"] == "active")
		{
			$values["active"] = true;
		}
		else
		{
			$values["expired"] = true;
		}
		
		return $values;
	}
	catch(\SmartHosting\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}






























































