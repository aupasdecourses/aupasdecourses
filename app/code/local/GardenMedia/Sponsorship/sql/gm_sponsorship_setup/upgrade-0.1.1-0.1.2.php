<?php

$salesrule = Mage::getModel('salesrule/rule')
    ->setName('Coupon de 10€ après 50€ de commande')
    ->setUsesPerCustomer(0)
    ->setIsActive(1)
    ->setConditionSerialized('a:6:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
    ->setActionsSerialized('a:6:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}')
    ->setStopRulesProcessing(0)
    ->setIsAdvanced(1)
    ->setSimpleAction('cart_fixed')
    ->setDiscountAmount(10)
    ->setDiscountStep(0)
    ->setSimpleFreeShipping(0)
    ->setApplyToShipping(0)
    ->setTimesUsed(0)
    ->setIsRss(1)
    ->setCouponType(2)
    ->setUseAutoGeneration(1)
    ->setUsesPerCoupon(1)
    ->setWebsiteIds(array(2,1,4))
    ->setCustomerGroupIds(array(1,2,6))
    ->setStoreLabels(
        array(
            0 => 'Bon de réduction de 10€',
            2 => '',
            1 => '',
            4 => ''
        )
    )
    ->save();


$config = Mage::getModel('core/config_data');
$config->setScope('default');
$config->setScopeId(0);
$config->setPath('gm_sponsorship/rewards/salesrule');
$config->setValue($salesrule->getId());
$config->save();
