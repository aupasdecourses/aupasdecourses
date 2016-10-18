<?php

/* To start we need to include abscract.php, which is located 
 * in /shell/abstract.php which contains Magento's Mage_Shell_Abstract 
 * class. 
 *
 * Since this .php is in /shell/Namespace/ we
 * need to include ../ in our require statement which means the
 * file we are including is up one directory from the current file location.
 */
require_once '../abstract.php';

class Pmainguet_DeleteCustomOptions extends Mage_Shell_Abstract{

	public function deletecustom()
	{

		$array=[
		3869,
	3882,
	3883,
	3887,
	3891,
	3892,
	3899,
	3907,
	3908,
	3911,
	3916,
	3917,
	3929,
	3930,
	3931,
	3932,
	3933,
	3938,
	3939,
	3941,
	3942,
	3943,
	3947,
	3948,
	3949,
	3952,
	3953,
	3954,
	3955,
	3957,
	3958,
	3959,
	3961,
	3962,
	3963,
	3964,
	3965,
	3966,
	3968,
	3969,
	3970,
	3971,
	3972,
	3973,
	3974,
	3977,
	3979,
	3982,
	3983,
	3984,
	3985,
	3986,
	3987,
	3988,
	3989,
	3991,
	3992,
	3993,
	3994,
	3995,
	3996,
	3997,
	3998,
	3999,
	4000,
	4001,
	4002,
	4010,
	4014,
	4017,
	4018,
	4019,
	4029,
	4030,
	4032,
	4034,
	4047,
	4066,
	4068,
	4078,
	4085,
	4093,
	4094,
	4107,
	4108,
	4109,
	4110,
	4111,
	4112,
	4113,
	4114,
	4115,
	4116,
	4117,
	4118,
	4119,
	4120,
	4121,
	4122,
	4123,
	4124,
	4125,
	4126,
	4127,
	4128,
	4129,
	4130,
	4131,
	4132,
	4133,
	4135,
	4136,
	4137,
	4138,
	4139,
	4142,
	4144,
	4146,
	4147,
	4148,
	4151,
	4153,
	4154,
	4155,
	4158,
	4159,
	4160,
	4161,
	4162,
	4163,
	4165,
	4166,
	4168,
	4170,
	4171,
	4172,
	4173,
	4175,
	4177,
	4178,
	4184,
	4185,
	4187,
	4188,
	4190,
	4194,
	4195,
	4196,
	4197,
	4198,
	4199,
	4200,
	4201,
	4203,
	4205,
	4206,
	4207,
	4209,
	4212,
	4214,
	4215,
	4216,
	4217,
	4218,
	4219,
	4220,
	4221,
	4222,
	4223,
	4224,
	4225,
	4226,
	4251,
	4252,
	4259,
	4264,
	4265,
	4277,
	4284,
	4465,
	4468,
	4469,
	4470,
	4471,
	4485,
	4601,
	4644,
	4661,
	4662,
	4663,
	4664,
	4665,
	4666,
	4667,
	4668,
	4669,
	4670,
	4671,
	4672,
	4673,
	4674,
	4675,
	4752,
	4753,
	4920,
	4946,
	4964,
	4986,
	4989,
	4990,
	4991,
	4992,
	4996,
	4997,
	4998,
	4999,
	5000,
	5001,
	5002,
	5003,
	5005,
	5122,
		];

		 foreach ($array as $id){
		 	$product = Mage::getModel("catalog/product")->load($id);
		 	echo "Processing ".$product->getName()."\n";
		 	if($product->getOptions() != array()){
		 		foreach ($product->getOptions() as $opt)
		 		{
		 			$opt->delete();
		 		}
		 	$product->setHasOptions(0);
		 	$product->setRequiredOptions(0);
		 	$product->save();
		 	echo "Options removed for ".$product->getName()."\n";
		 	} else {
		 		echo "NO OPTIONS FOR ".$product->getName()."\n";
		 	}
		 }
	}

	// Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $steps = ['deletecustom'];
        //get argument passed to shell script
        $step = $this->getArg('step');
        if (in_array($step, $steps)) {
            $this->$step();
        } else {
            echo "STEP MUST BE ONE OF THESE:\n";
            foreach ($steps as $s) {
                echo $s.",\n";
            }
        }
    }

}

$shell = new Pmainguet_DeleteCustomOptions();
$shell->run();
