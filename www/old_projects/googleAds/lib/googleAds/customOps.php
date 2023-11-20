<?php


require  '/var/www/core/google-ads-php/vendor/autoload.php';

use GetOpt\GetOpt;
use Google\Ads\GoogleAds\Examples\Utils\ArgumentNames;
use Google\Ads\GoogleAds\Examples\Utils\ArgumentParser;
use Google\Ads\GoogleAds\Lib\V3\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V3\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V3\GoogleAdsException;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\V3\Enums\GeoTargetConstantStatusEnum\GeoTargetConstantStatus;
use Google\Ads\GoogleAds\V3\Enums\CriterionTypeEnum\CriterionType;
use Google\Ads\GoogleAds\V3\Enums\KeywordMatchTypeEnum\KeywordMatchType;
use Google\Ads\GoogleAds\V3\Common\KeywordInfo;
use Google\Ads\GoogleAds\V3\Common\LocationInfo;
use Google\Ads\GoogleAds\Lib\V3\GoogleAdsServerStreamDecorator;
use Google\Ads\GoogleAds\Util\V3\ResourceNames;
use Google\Ads\GoogleAds\V3\Common\AddressInfo;
use Google\Ads\GoogleAds\V3\Errors\GoogleAdsError;
use Google\Ads\GoogleAds\V3\Resources\CampaignCriterion;
use Google\Ads\GoogleAds\V3\Services\CampaignCriterionOperation;
use Google\Ads\GoogleAds\V3\Services\GeoTargetConstantSuggestion;
use Google\Ads\GoogleAds\V3\Services\SuggestGeoTargetConstantsRequest\GeoTargets;
use Google\Ads\GoogleAds\V3\Services\GoogleAdsRow;
use Google\Ads\GoogleAds\V3\Services\SearchGoogleAdsStreamResponse;
use Google\ApiCore\ApiException;
use Google\Protobuf\StringValue;
use Google\Protobuf\BoolValue;
use Google\Protobuf\DoubleValue;



// vrati vsechny kampane na uctu
class CustomOps
{
    const LOCALE = 'cz';                                                                        // Locale is using ISO 639-1 format.
    const COUNTRY_CODE = 'CZ';
    const PAGE_SIZE = 1000;                                                                     // pocet zaznamu vypsanych na starnku z query
    
    // vrati goggle ads klienta pro logovani
    public static function login()
    {
        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile()->build();

        // Construct a Google Ads client configured from a properties file and the
        // OAuth2 credentials above.
        $googleAdsClient = (new GoogleAdsClientBuilder())
            ->fromFile()
            ->withOAuth2Credential($oAuth2Credential)
            ->build();   
        
        return($googleAdsClient);
    }

    // vrati vsechny kampane podle cisla uctu
    public static function getCampaigns(int $customerId)
    {
        $googleAdsClient = self::login();                                                                       // login
        $googleAdsServiceClient = $googleAdsClient->getGoogleAdsServiceClient();
        
        // Creates a query that retrieves all campaigns.
        $query = 'SELECT campaign.id, campaign.name FROM campaign ORDER BY campaign.id';
        $stream = $googleAdsServiceClient->searchStream($customerId, $query);

        // Iterates over all rows in all messages and prints the requested field values for
        // the campaign in each row.
        foreach ($stream->readAll() as $response) {
            foreach ($response->getResults() as $googleAdsRow) {
                $CampaignId = $googleAdsRow->getCampaign()->getId()->getValue();
                $to_return[$CampaignId]['id'] = $googleAdsRow->getCampaign()->getId()->getValue();
                $to_return[$CampaignId]['name'] = $googleAdsRow->getCampaign()->getName()->getValue();
            }
        }
        return($to_return);
    }
    
    // vrati vsechny lokace podle cisla uctu a id kampane
    public static function getCampaignLocations(int $customerId, int $campaignId) 
    {
        $googleAdsClient = self::login();                                                                       // login
        $googleAdsServiceClient = $googleAdsClient->getGoogleAdsServiceClient();
        // Creates a query that retrieves campaign criteria.
        $query = 'SELECT campaign.id, campaign_criterion.campaign, '
                . 'campaign_criterion.criterion_id, campaign_criterion.type, '
                . 'campaign_criterion.negative, campaign_criterion.keyword.text, '
                . 'campaign_criterion.keyword.match_type FROM campaign_criterion'
            . ' WHERE campaign.id = ' . $campaignId;

        // Issues a search request by specifying page size.
        $response = $googleAdsServiceClient->search($customerId, $query, ['pageSize' => self::PAGE_SIZE]);

        // Iterates over all rows in all pages and prints the requested field values for
        // the campaign criterion in each row.
        foreach ($response->iterateAllElements() as $googleAdsRow) {
            $campaignCriterion = $googleAdsRow->getCampaignCriterion();
            $to_return[] = $campaignCriterion->getCriterionId()->getValue();
        }
        return($to_return);
    }
    
    //vrati info o lokacich podle jejich Id
    public static function getGeoTargetConstantById(array $locationId, string $locale = 'cz', string $countryCode = 'CZ')
    {
        $geoTargets = preg_filter('/^/', 'geoTargetConstants/', $locationId);                                   // pridam k id mist prefix
        
        $googleAdsClient = self::login();                                                                       // login
        $geoTargetConstantServiceClient = $googleAdsClient->getGeoTargetConstantServiceClient();
 
        $geo_target_constants = [];
        foreach ($geoTargets as $geoTarget) {
            $geo_target_constants[] = new StringValue(['value' => $geoTarget]);
        }

        $response = $geoTargetConstantServiceClient->suggestGeoTargetConstants(
            new StringValue(['value' => $locale]),
            new StringValue(['value' => $countryCode]),
            ['geoTargets' => new GeoTargets(['geo_target_constants' => $geo_target_constants])]
        );

        // Iterates over all geo target constant suggestion objects and prints the requested field
        // values for each one.
        foreach ($response->getGeoTargetConstantSuggestions() as $geoTargetConstantSuggestion) {
            $id = str_replace('geoTargetConstants/', '', $geoTargetConstantSuggestion->getGeoTargetConstant()->getResourceName());
            $to_return[$id]['resourceName'] = $geoTargetConstantSuggestion->getGeoTargetConstant()->getResourceName();
            $to_return[$id]['name'] = $geoTargetConstantSuggestion->getGeoTargetConstant()->getNameUnwrapped();
            $to_return[$id]['countryCode'] = $geoTargetConstantSuggestion->getGeoTargetConstant()->getCountryCodeUnwrapped();
            $to_return[$id]['targetType'] = $geoTargetConstantSuggestion->getGeoTargetConstant()->getTargetTypeUnwrapped();
        }
        return($to_return);
    }
    
    // smaze lokaci podle cisla uctu, id kampane a id lokace
    public static function removeCampaignLocation(int $customerId, int $campaingId, array $criterionIds) {
        
            $googleAdsClient = self::login();                                                                       // login        
        
        foreach ($criterionIds as $key => $criterionId) {
            // Creates ad group criterion resource name.
            $CampaignCriterionResourceName = ResourceNames::forCampaignCriterion($customerId, $campaingId, $criterionId);
            
            // Constructs an operation that will remove the keyword with the specified resource name.
            $CampaignCriterionOperation = new CampaignCriterionOperation();
            $CampaignCriterionOperation->setRemove($CampaignCriterionResourceName);
            
            // Issues a mutate request to remove the ad group criterion.
            $CampaignCriterionServiceClient = $googleAdsClient->getCampaignCriterionServiceClient();
            $response = $CampaignCriterionServiceClient->mutateCampaignCriteria(
                $customerId,
                [$CampaignCriterionOperation]
            );
            
            $removedCampaignCriterion = $response->getResults()[0];
            
            // Prints the resource name of the removed ad group criterion.
            printf(
                "Removed ad group criterion with resource name: '%s'%s",
                $removedCampaignCriterion->getResourceName(),
                '<br>'
            );            
        }
    }
    
    // vytvori lokaci podle cisla uctu, id kampane a id lokace 
    public static function createLocation(int $customerId, int $campaignId, array $locationIds) {
        // Constructs a campaign criterion for the specified campaign ID using the specified
        // location ID.
        $campaignResourceName = ResourceNames::forCampaign($customerId, $campaignId);
        
        foreach ($locationIds as $key => $locationId) {                                                                         // vytvorim pozadavky na vytvoreni lokace pro vsechny zadane lokace
            $campaignCriterion = new CampaignCriterion([
                // Creates a location using the specified location ID.
                'location' => new LocationInfo([
                    'geo_target_constant' => new StringValue(['value' => ResourceNames::forGeoTargetConstant($locationId)])
                ]),
                'campaign' => new StringValue(['value' => $campaignResourceName])
            ]);

            $operations[] = new CampaignCriterionOperation(['create' => $campaignCriterion]);
        }

        
        $googleAdsClient = self::login();                                                                       // login
        
        $campaignCriterionServiceClient = $googleAdsClient->getCampaignCriterionServiceClient();                // Issues a mutate request to add the campaign criterion.
        $response = $campaignCriterionServiceClient->mutateCampaignCriteria($customerId, $operations);

        printf("Added %d campaign criteria:%s", $response->getResults()->count(), PHP_EOL);

        foreach ($response->getResults() as $addedCampaignCriterion) {
            print $addedCampaignCriterion->getResourceName() . PHP_EOL;
        }        
    }    
    
}

