/* @ def 	:  define of Global dependent input if not followed !, Try on window object
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
// define dependent field
/***
	-- var mandatory survey 
**/
// function hi(){alert('apa kabar');}
var Mandat_SurveyQuest = [];
var Mandat_SurveyAns = [];
var PayMethod =( Ext.Ajax({ 
					url 	: '../class/class.SaveAxa.php', 
					method :'GET', 
					param 	: { action	: '_json_pay_method'}
					}).json() );

Ext.DOM.Variable = {
	prefix : false,
	expire : false
}
// var a = [
			// {"cmp" : "PayerCreditCardNum", "msg" : "Payer Credit Card Number is empty"},
			// {"cmp" : "PayerCreditCardExpDate", "msg" : "Expiration Date is empty"}
		// ];

// for (var i in a) {
	// console.log(a[i].msg);
// }

Ext.DOM._benefiecery = {
    field: [
        'BenefRelationshipTypeId',
        'BenefSalutationId',
        'BenefFirstName',
        'BenefLastName',
		'BenefGenderId',
		'BenefDOB',
        'BenefPercentage'
    ],

    chars: 'Be',
    code: 0
}


// define holder field
Ext.DOM.Insured = {
    field: {
        InsuredIdentificationTypeId: {
            keys: false,
            warn: 'ID Type is empty',
            number: false,
            clear: true
        },
        InsuredIdentificationNum: {
            keys: false,
            warn: 'ID Number is empty',
            number: false,
            clear: true
        },
        InsuredRelationshipTypeId: {
            keys: false,
            warn: 'Relation Type is empty',
            number: false,
            clear: true
        },
        InsuredSalutationId: {
            keys: false,
            warn: 'Title is empty',
            number: false,
            clear: true
        },
        InsuredFirstName: {
            keys: true,
            warn: 'First Name is empty',
            number: false,
            clear: true
        },
        InsuredLastName: {
            keys: false,
            warn: 'Last Name is empty',
            number: false,
            clear: true
        },
        InsuredGenderId: {
            keys: true,
            warn: 'Gender is empty',
            number: false,
            clear: true
        },
        InsuredDOB: {
            keys: true,
            warn: 'DOB is empty',
            number: false,
            clear: true
        },
		InsuredPOB: {
            keys: false,
            warn: 'POB is empty',
            number: false,
            clear: true
        },
        InsuredAge: {
            keys: true,
            warn: 'Age is empty or Payer DOB not in range!',
            number: true,
            clear: true
        },
        InsuredPayMode: {
            keys: true,
            warn: 'Payment Mode is empty',
            number: false,
            clear: false
        },
        InsuredPlanType: {
            keys: true,
            warn: 'Plan Type is empty',
            number: false,
            clear: false
        },
        InsuredPremi: {
            keys: true,
            warn: 'Premi is empty',
            number: false,
            clear: true
        }
    },

    chars: 'Ho',
    code: 2
}
// define payers field
Ext.DOM._PayersData = [
    'PayerSalutationId',
    'PayerFirstName',
    'PayerLastName',
    'PayerGenderId',
    'PayerDOB',
    'PayerAddressLine1',
    'PayerIdentificationTypeId',
    'PayerIdentificationNum',
    'PayerMobilePhoneNum',
    'PayerMobilePhoneNum2',
    'PayerCity',
    'PayerAddressLine2',
    'PayerHomePhoneNum',
    'PayerHomePhoneNum2',
    'PayerZipCode',
    'PayerAddressLine3',
    'PayerOfficePhoneNum',
    'PayerOfficePhoneNum2',
    'PayerProvinceId',
    'PayerAddressLine4',
    'PayerCreditCardNum',
    'PayersBankId',
    'PayerFaxNum',
    'PayerCreditCardExpDate',
    'CreditCardTypeId',
    'PayerEmail'
]

/* @ jquery :	fucked 
 * @ render on ready document
 * @ will ender by "tabs "
 */

$(document).ready(function() {

    /* @ jquery :	fucked 
     * @ render on ready document
     * @ will ender by "tabs "
     */
    // $("#tabs").tabs();
	// $( "#tabs" ).tabs( "option", "disabled", [0,1,2,3,4,5,6,7]);
	$("#tabs").tabs({
		"selected" : 0,
		"disabled" : [0,1,2,3,4,5,6,7]
	});
    /*
     * @ get all data date picker srializer
     * @ is simple get data asumsion
     */
    $(".date").datepicker({
        buttonImage: '../gambar/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '1945:2030',
        dateFormat: 'dd-mm-yy',
        onSelect: function(e) {
            var _a = $(this).attr("id"),
                _b = _a.substring(0, 2),
                _c = e.split('-'),
                _d = _c[2] + '-' + _c[1] + '-' + _c[0];
			if( Ext.Cmp('ProductId').getValue() != '' )
			{
				if (_c.length > 2) {
					if(_b == 'In')
					{
						var JSnum = (
							Ext.Ajax({
								url: '../class/class.SaveAxa.php',
								method: 'GET',
								param: {
									action: '_get_age',
									ProductId: Ext.Cmp("ProductId").getValue(),
									GroupPremi: Ext.Cmp("InsuredGroupPremi").getValue(),
									DOB: _d.trim()
								}
							}).json()
						);
					
						if (JSnum.success) {
							Ext.Cmp('InsuredAge').setValue(JSnum.personal_age);
							Ext.Cmp("InsuredPremi").setValue(
								Ext.Ajax({
									url: '../class/class.SaveAxa.php',
									method: 'GET',
									param: {
										action: '_get_premi',
										ProductId: Ext.Cmp("ProductId").getValue(),
										PersonalAge: Ext.Cmp("InsuredAge").getValue(),
										PayModeId: Ext.Cmp("InsuredPayMode").getValue(),
										PlanTypeId: Ext.Cmp("InsuredPlanType").getValue(),
										GroupPremi: Ext.Cmp("InsuredGroupPremi").getValue()
									}
								}).json().personal_premi
							);
						} 
						else {
							Ext.Msg(JSnum.Error).Error();
							Ext.Cmp('InsuredAge').setValue('');
							Ext.Cmp("InsuredPremi").setValue('')
						}

						Ext.Cmp('InsuredAge').disabled(true);
						Ext.Cmp("InsuredPremi").disabled(false);
					}
					else if(_b == 'Pa')
					{
						if(Ext.Cmp("ProductId").getValue())
						{
							var JSnum = (
								Ext.Ajax({
									url: '../class/class.SaveAxa.php',
									method: 'GET',
									param: {
										action: '_get_age_payer',
										ProductId: Ext.Cmp("ProductId").getValue(),
										GroupPremi: "2",
										DOB: _d.trim()
									}
								}).json()
							);
							//Ext.Cmp('CopyDataInsured').getChecked()
							if (JSnum.success) {
								Ext.Cmp('PayerAge').setValue(JSnum.personal_age);
								if(Ext.Cmp('CopyDataInsured').getChecked())
								{
									Ext.Cmp('InsuredDOB').setValue(_d.trim());
									Ext.Cmp('InsuredAge').setValue(JSnum.personal_age);
									Ext.DOM.getPremi();
								}
							} 
							else {
								Ext.Msg(JSnum.Error).Error();
								Ext.Cmp('PayerAge').setValue('');
								if(Ext.Cmp('CopyDataInsured').getChecked())
								{
									Ext.Cmp('InsuredDOB').setValue(_d.trim());
									Ext.Cmp('InsuredAge').setValue('');
									Ext.Cmp('InsuredPremi').setValue('');
								}
							}
						}
						else{
							Ext.Msg("Please choose Product!").Error();
							Ext.Cmp('PayerAge').setValue('');
						}
					}
				}
			}
			else{
				alert('Please, choose product!');
			}
        }
    });

    /* @ Ext 		: autoload   
     * @ render 	: on ready document
     * @ will ender by "tabs "
     */

    Ext.DOM.WindowDisabled = (function(e) {
        return rad = {
            /* @ Ext 		: autoload   
             * @ render 	: on ready document
             * @ will ender by "tabs "
             */
            benefiecery: function() {
                for (var p = 1; p <= e; p++) {
                    for (var a in Ext.DOM._benefiecery.field) {
                        Ext.Cmp(Ext.DOM._benefiecery.field[a] + "_" + p).disabled(true);
                    }
                }
            },

            /* @ Ext 		: autoload   
             * @ render 	: on ready document
             * @ will ender by "tabs "
             */
            Insured: function() {
                for (var i in Ext.DOM.Insured.field) {
                    Ext.Cmp(i).disabled(false);
                }
            }
        }
    });
	
    // disabled first loqding 
    Ext.DOM.WindowDisabled(4).benefiecery();
    Ext.DOM.WindowDisabled(1).Insured();
	
	// var availableTags = [
      // "ActionScript",
      // "AppleScript",
      // "Asp",
      // "BASIC",
      // "C",
      // "C++",
      // "Clojure",
      // "COBOL",
      // "ColdFusion",
      // "Erlang",
      // "Fortran",
      // "Groovy",
      // "Haskell",
      // "Java",
      // "JavaScript",
      // "Lisp",
      // "Perl",
      // "PHP",
      // "Python",
      // "Ruby",
      // "Scala",
      // "Scheme"
    // ];
    // $( "#PayerPOB" ).autocomplete({
      // source: availableTags
    // });
	
	// $( "#PayerPOB" ).autocomplete({
		// source: "../class/class.SaveAxa.php",
		// minLength: 3
	// });
	
	var cache = {};
	$( ".suggestcity" ).autocomplete({
		minLength: 3,
		delay: 300,
		source: function( request, response ) {
			var term = request.term;
			if ( term in cache ) {
				response( cache[ term ] );
				return;
			}
			request.action = 'suggest_city';
			$.getJSON( "../class/class.SaveAxa.php", request, function( data, status, xhr ) {
				cache[ term ] = data;
				response( data );
			});
		}
	});
});

/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

Ext.DOM.ResetInsured = function() {
    for (var i in Ext.DOM.Insured.field) {
        if (Ext.DOM.Insured.field[i].clear) {
            if(i == 'InsuredRelationshipTypeId' && parseInt(Ext.Cmp('InsuredGroupPremi').getValue()) == 2)
			{
				Ext.Cmp(i).setValue(79);
				Ext.Cmp(i).disabled(true);
			}
			else{
				Ext.Cmp(i).setValue('');
				Ext.Cmp(i).disabled(false);
			}
        }
    }
}

/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
Ext.DOM.ValidPrefix = function()
{
	var ccn = Ext.Cmp('PayerCreditCardNum').getValue();
	var exp = Ext.Cmp('PayerCreditCardExpDate').getValue();
	
	var jsNum = (
		Ext.Ajax({
			url: '../class/class.SaveAxa.php',
			method: 'GET',
			param: {
				action: '_get_valid_prefix',
				card_num: ccn,
				expired_date: exp
			}
		}).json()
	);
	
	Ext.Cmp('error_message_html').setText(jsNum.img);
	Ext.DOM.Variable.prefix = jsNum.result;
}

Ext.DOM.ValidExpired = function()
{
	var exp = Ext.Cmp('PayerCreditCardExpDate').getValue();
	// alert(exp.length);
	if(exp.length == 2)
	{
		Ext.Cmp('PayerCreditCardExpDate').setValue(exp+"/");
	}
	
	var jsNum = (
		Ext.Ajax({
			url: '../class/class.SaveAxa.php',
			method: 'GET',
			param: {
				action: '_get_valid_expire',
				expired_date: exp
			}
		}).json()
	);
	
	Ext.Cmp('error_message_exp').setText(jsNum.img);
	Ext.DOM.Variable.expire = jsNum.result;
}

Ext.DOM.ClearInsured = function() {
    Ext.Ajax({
        url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_detail',
            InsuredPolicyNumber: Ext.Cmp('InsuredPolicyNumber').getValue(),
            GroupPremi: Ext.Cmp('InsuredGroupPremi').getValue()
        },
        ERROR: function(e) {
            var ERR = JSON.parse(e.target.responseText);
            if (ERR.success) {
                Ext.Cmp('InsuredIdentificationTypeId').setValue(ERR.data.IdentificationTypeId);
                Ext.Cmp('InsuredIdentificationNum').setValue(ERR.data.InsuredIdentificationNum);
                Ext.Cmp('InsuredRelationshipTypeId').setValue(ERR.data.RelationshipTypeId);
                Ext.Cmp('InsuredSalutationId').setValue(ERR.data.SalutationId);
                Ext.Cmp('InsuredFirstName').setValue(ERR.data.InsuredFirstName);
                Ext.Cmp('InsuredLastName').setValue(ERR.data.InsuredLastName);
                Ext.Cmp('InsuredGenderId').setValue(ERR.data.GenderId);
                Ext.Cmp('InsuredDOB').setValue(ERR.data.InsuredDOB);
                Ext.Cmp('InsuredAge').setValue(ERR.data.InsuredAge);
                Ext.Cmp('InsuredPayMode').setValue(ERR.data.PayModeId);
                Ext.Cmp('InsuredPlanType').setValue(ERR.data.ProductPlan);
                Ext.Cmp('InsuredPremi').setValue(ERR.data.ProductPlanPremium);
            } else {
                Ext.DOM.ResetInsured();
            }
        }
    }).post();

    if (parseInt(Ext.Cmp('InsuredGroupPremi').getValue()) == 2) {
        Ext.Cmp("CopyDataInsured").disabled(false);
		Ext.Cmp('InsuredRelationshipTypeId').setValue(79);
		Ext.Cmp('InsuredRelationshipTypeId').disabled(true);
    } else {
        Ext.Cmp("CopyDataInsured").setUnchecked();
        Ext.Cmp("CopyDataInsured").disabled(true);
		Ext.Cmp('InsuredRelationshipTypeId').setValue("");
		Ext.Cmp('InsuredRelationshipTypeId').disabled(false);
    }
}

/* @ def 	:  CopyDataInsured 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

Ext.DOM.CopyDataInsured = function(opt) {
	(opt.checked ? (
        Ext.Cmp('InsuredIdentificationTypeId').setValue(Ext.Cmp('PayerIdentificationTypeId').getValue()),
        Ext.Cmp('InsuredIdentificationTypeId').setValue(Ext.Cmp('PayerIdentificationTypeId').getValue()),
        Ext.Cmp('InsuredIdentificationNum').setValue(Ext.Cmp('PayerIdentificationNum').getValue()),
        Ext.Cmp('InsuredSalutationId').setValue(Ext.Cmp('PayerSalutationId').getValue()),
        Ext.Cmp('InsuredFirstName').setValue(Ext.Cmp('PayerFirstName').getValue()),
        Ext.Cmp('InsuredLastName').setValue(Ext.Cmp('PayerLastName').getValue()),
        Ext.Cmp('InsuredGenderId').setValue(Ext.Cmp('PayerGenderId').getValue()),
        Ext.Cmp('InsuredPOB').setValue(Ext.Cmp('PayerPOB').getValue()),
        Ext.Cmp('InsuredDOB').setValue(Ext.Cmp('PayerDOB').getValue()),
		Ext.Cmp('InsuredAge').setValue((Ext.Cmp('PayerAge').getValue()?Ext.Cmp('PayerAge').getValue():'')),
		
		Ext.Cmp('InsuredIdentificationTypeId').disabled(true),
        Ext.Cmp('InsuredIdentificationTypeId').disabled(true),
        Ext.Cmp('InsuredIdentificationNum').disabled(true),
        Ext.Cmp('InsuredSalutationId').disabled(true),
        Ext.Cmp('InsuredFirstName').disabled(true),
        Ext.Cmp('InsuredLastName').disabled(true),
        Ext.Cmp('InsuredGenderId').disabled(true),
        Ext.Cmp('InsuredPOB').disabled(true),
        Ext.Cmp('InsuredDOB').disabled(true),
		Ext.Cmp('InsuredAge').disabled(true),
		
		Ext.DOM.getPremi()
    ) : (
        Ext.Cmp('InsuredIdentificationTypeId').setValue(''),
        Ext.Cmp('InsuredIdentificationTypeId').setValue(''),
        Ext.Cmp('InsuredIdentificationNum').setValue(''),
        Ext.Cmp('InsuredSalutationId').setValue(''),
        Ext.Cmp('InsuredFirstName').setValue(''),
        Ext.Cmp('InsuredLastName').setValue(''),
        Ext.Cmp('InsuredGenderId').setValue(''),
        Ext.Cmp('InsuredPOB').setValue(''),
        Ext.Cmp('InsuredDOB').setValue(''),
        Ext.Cmp('InsuredAge').setValue(''),
		
		Ext.Cmp('InsuredIdentificationTypeId').disabled(false),
        Ext.Cmp('InsuredIdentificationTypeId').disabled(false),
        Ext.Cmp('InsuredIdentificationNum').disabled(false),
        Ext.Cmp('InsuredSalutationId').disabled(false),
        Ext.Cmp('InsuredFirstName').disabled(false),
        Ext.Cmp('InsuredLastName').disabled(false),
        Ext.Cmp('InsuredGenderId').disabled(false),
        Ext.Cmp('InsuredPOB').disabled(false),
        Ext.Cmp('InsuredDOB').disabled(false),
		Ext.Cmp('InsuredAge').disabled(false),
		
		Ext.DOM.getPremi()
    ))
}

Ext.DOM.CardTypeChange = function() {
	Ext.Ajax({
		url: '../class/class.SaveAxa.php',
		method: 'GET',
		param: {
			action: 'CmbBankByCardType',
			CardType :Ext.Cmp('CreditCardTypeId').getValue()
		}
	}).load("dyn_bank");
}
/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

Ext.document('document').ready(function() {
    Ext.Cmp('PayerIdentificationNum').listener({
        'onKeyup': function(e) {
            Ext.Set(e.currentTarget.id).IsNumber();
        }
    });
	
    Ext.Cmp('PayerMobilePhoneNum').listener({
        'onKeyup': function(e) {
            Ext.Set(e.currentTarget.id).IsNumber();
        }
    });
	
	Ext.Cmp('InsuredPlanType').listener({
        'onChange': function(e) {
            Ext.DOM.benefInsured();
			getPremi(Ext.Cmp('InsuredPlanType').getValue());
        }
    });
	
	Ext.Cmp('PayerPaymentType').listener({
        'onChange': function(e) {
			var PayType = Ext.Cmp('PayerPaymentType').getValue();
			for (var i in PayMethod.form) {
				// alert(PayMethod.form[i]);
				if(PayType==i)
				{
					$( "#"+PayMethod.form[i] ).show();
				}
				else
				{
					$( "#"+PayMethod.form[i] ).hide();
					if(PayType!="")
					{
						window[PayMethod.reset[PayType]]();
					}
				}
			}
			// Ext.Ajax({
				// url: '../class/class.SaveAxa.php',
				// method: 'GET',
				// param: {
					// action: '_card_type_pay',
					// Pay_Type :PayType
				// }
			// }).load("dyn_card_type");
			Ext.DOM.loadCardType();
        }
    });
	
	Ext.Cmp('PayerIdentificationNum').listener({
        'onKeyup': function(e) {
            Ext.Set(e.currentTarget.id).IsNumber();
        }
    });
	
    Ext.Cmp('PayerHomePhoneNum').listener({
        'onKeyup': function(e) {
            Ext.Set(e.currentTarget.id).IsNumber();
        }
    });
	
    Ext.Cmp('PayerOfficePhoneNum').listener({
        'onKeyup': function(e) {
            Ext.Set(e.currentTarget.id).IsNumber();
        }
    });
	
    Ext.Cmp('PayerCreditCardExpDate').listener({
        'onKeyup': function(e) {
            Ext.Set(e.currentTarget.id).IsNumber();
			Ext.DOM.ValidExpired();
        }
    });
	
	Ext.Cmp('PayerDOB').listener({
        'onKeyup': function(e) {
            var DOB = Ext.Cmp('PayerDOB').getValue();
			var patt = new RegExp(/^\d{2}-\d{2}-\d{4}/);
			var res = patt.test(DOB);
			if(res)
			{
				var JSnum = getAgePayer();
				
				if(JSnum.success==1)
				{
					
					Ext.Cmp('PayerAge').setValue(JSnum.personal_age);
				}
				else{
					Ext.Cmp('PayerAge').setValue(JSnum.personal_age);
				}
				
			}
			else
			{
				
				Ext.Cmp('PayerAge').setValue('0');
			}
			
        }
    });
	
	Ext.Cmp('PayerCreditCardNum').listener({
        'onKeyup': function(e) {
            Ext.Set(e.currentTarget.id).IsNumber();
            Ext.DOM.ValidPrefix();
        }
    });
	
	Ext.Cmp('SavingAccount').listener({
        'onKeyup': function(e) {
            Ext.Set(e.currentTarget.id).IsNumber();
        }
    });
	
    Ext.Cmp('PayerFaxNum').listener({
        'onKeyup': function(e) {
            Ext.Set(e.currentTarget.id).IsNumber();
        }
    });
	
	// Ext.Cmp('IvrPayMethod').listener({
         // 'onChange' : function(e) {
             // Ext.DOM.splitintoivr();
         // }   
     // });
	
    Ext.Cmp('ProductId').listener({
        'onChange': function(e) {
			Ext.DOM.LoadPayMode();
            Ext.DOM.LoadPlanType();
			Ext.DOM.LoadGroupPremi();
			Ext.DOM.benefInsured();
			Ext.Cmp("CopyDataInsured").setUnchecked();
			Ext.Cmp("CopyDataInsured").disabled(true);
			Ext.DOM.Benefit();
			Ext.DOM.questioner();
			Ext.DOM.BenefActive();
			if(Ext.Cmp('ProductId').getValue())
			{
				if(Ext.Cmp('categorycode').getValue() == 'FPA'){
					$( "#tabs" ).tabs( "option", "selected", 0 );
					$( "#tabs" ).tabs( "option", "disabled", [7]);
					Ext.Cmp('PayerCreditCardNum').disabled(false);
					Ext.Cmp('PayerCreditCardExpDate').disabled(false);
					Ext.Cmp('PayersBankId').disabled(false);
					Ext.Cmp('CreditCardTypeId').disabled(false);
					Ext.Cmp('PayerPaymentType').disabled(false);
					Ext.Cmp("cbxDataPayer").setUnchecked();
					Ext.Cmp("cbxDataPayer").disabled(true);
					Ext.Cmp('xsellinfo').setText("");
				}else if(Ext.Cmp('categorycode').getValue() == 'APE') {
					if(Ext.Cmp('PayerValidXsell').getValue() == "TRUE")
					{
						Ext.Cmp("cbxDataPayer").disabled(false);
						Ext.Cmp("cbxDataPayer").setUnchecked();
						Ext.Cmp('xsellinfo').setText("");
					}
					$( "#tabs" ).tabs( "option", "disabled", []);
					$( "#tabs" ).tabs( "option", "selected", 0 );
					
					Ext.Cmp('ivr_list').setText("");
					Ext.Cmp('IvrPayMethod').setValue("");
					Ext.Cmp('PayerCreditCardNum').disabled(true);
					Ext.Cmp('PayerCreditCardExpDate').disabled(true);
					Ext.Cmp('PayerPaymentType').disabled(true);
					Ext.Cmp("PayerPaymentType").setValue('');
					Ext.Cmp('CreditCardTypeId').disabled(true);
					Ext.Cmp("CreditCardTypeId").setValue('');
					
					Ext.Cmp('PayersBankId').disabled(true);
					Ext.Cmp("PayersBankId").setValue('');
					for (var i in PayMethod.form) {
						$( "#"+PayMethod.form[i] ).hide();
					}
					// Ext.Cmp('PayersBankId').disabled(true);
					// Ext.Cmp('CreditCardTypeId').disabled(true);
				}
				// $( "#tabs" ).tabs( "option", "disabled", []);
			}
			else{
				$( "#tabs" ).tabs( "option", "selected", 0 );
				$( "#tabs" ).tabs( "option", "disabled", [0,1,2,3,4,5,6,7]);
				Ext.Cmp("cbxDataPayer").disabled(true);
			}
		}
    });
	
    Ext.Cmp('PayerEmail').listener({
    	'onChange':function(e){
    		//var email = $(this).value();
    		if(!Ext.DOM.Ismail()){
    			Ext.Cmp('PayerEmail').setFocus(true,10);
    			Ext.Cmp('PayerEmail').setValue('');
    		};
    	}
    });
    Ext.Cmp('TRANSACTION').listener({
        'onClick': function(e) {
            Ext.DOM.Transaction();
        }
    });
	
	Ext.DOM.CopyData();
	Ext.DOM.benefInsured();
});

function getAgePayer()
{
	var PayerDOB = Ext.Cmp('PayerDOB').getValue();
		 _c = PayerDOB.split('-'),
         _d = _c[2] + '-' + _c[1] + '-' + _c[0];
	if( Ext.Cmp('ProductId').getValue() != '' )
	{
		var JSnum = (
			Ext.Ajax({
				url: '../class/class.SaveAxa.php',
				method: 'GET',
				param: {
					action: '_get_age_payer',
					ProductId: Ext.Cmp("ProductId").getValue(),
					GroupPremi: "2",
					DOB: _d.trim()
				}
			}).json()
		);
		return JSnum;
	}
	else
	{
		alert('Please, choose product!');
		return false;
	}
		
}
/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
Ext.DOM.Ismail = function(){
	var email = Ext.Cmp('PayerEmail').getValue();
	var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if(!regex.test(email)){
		Ext.Msg("Email Not Valid").Info();
		return false;
	}else{
		return true;
	}
}

Ext.DOM.validInsured = function() {
    next_process = false;

    var JSnum = (
        Ext.Ajax({
            url: '../class/class.SaveAxa.php',
            method: 'GET',
            param: {
                action: '_get_valid_ins',
                CustomerId: Ext.Cmp("CustomerId").getValue(),
                ProductId: Ext.Cmp('ProductId').getValue(),
                MemberGroup: Ext.Cmp('InsuredGroupPremi').getValue()
            }
        }).json()
    );

    if (JSnum.result) {
        if (JSnum.MemberGroup != 1) {
            next_process = false;
        } else {
            next_process = true;
        }
    } else {
        next_process = true;
    }

    return next_process;
}

Ext.DOM.benefInsured = function() {
	Ext.Ajax({
		url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_benefInsured',
            plan: Ext.Cmp("InsuredPlanType").getValue(),
			ProductId: Ext.Cmp('ProductId').getValue()
        }
	}).load("benefit")
}

Ext.DOM.BenefActive = function()
{
	var JSnum = (
        Ext.Ajax({
            url: '../class/class.SaveAxa.php',
            method: 'GET',
            param: {
                action: '_check_benef',
                ProductId: Ext.Cmp('ProductId').getValue()
            }
        }).json()
    );
	
	if (JSnum.result)
	{
		Ext.Cmp("Benefeciery").disabled(false);
	}
	else{
		Ext.Cmp("Benefeciery").setUnchecked();
		Ext.Cmp("Benefeciery").disabled(true);
	}
	
	for(var p = 1;p < 5;p++){
		for (var i in Ext.DOM._benefiecery.field) {
            Ext.Cmp(Ext.DOM._benefiecery.field[i] + "_" + p).disabled(true);
            Ext.Cmp(Ext.DOM._benefiecery.field[i] + "_" + p).setValue('');
        }
	}
}

Ext.DOM.Transaction = function() {
    Ext.Ajax({
        url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_transaction',
            CustomerId: Ext.Cmp("CustomerId").getValue()
        }
    }).load("Transaction");
}

Ext.DOM.questioner = function() {

	/***
	-- Store mandatory questioner
	**/
	Mandat_SurveyQuest =(Ext.Ajax({ 
					url 	: '../class/class.SaveAxa.php', 
					method :'GET', 
					param 	: { 
					action	: '_get_mandat_quest',
					ProductId: Ext.Cmp('ProductId').getValue(),
							}
					}).json() );
					
	Mandat_SurveyAns =(Ext.Ajax({ 
					url 	: '../class/class.SaveAxa.php', 
					method :'GET', 
					param 	: { 
					action	: '_get_mandat_ans',
					ProductId: Ext.Cmp('ProductId').getValue(),
							}
					}).json() );
    Ext.Ajax({
        url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_questioner',
            ProductId: Ext.Cmp('ProductId').getValue(),
            CustomerId: Ext.Cmp('CustomerId').getValue(),
			QuestinerType : 1
        }
    }).load("survey");
	
	Ext.Ajax({
        url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_questioner',
            ProductId: Ext.Cmp('ProductId').getValue(),
            CustomerId: Ext.Cmp('CustomerId').getValue(),
			QuestinerType :2
        }
    }).load("uw");
}

Ext.DOM.Benefit = function() {
    Ext.Ajax({
        url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_benefit',
            ProductId: Ext.Cmp('ProductId').getValue()
        }
    }).load("Benefit")
}
/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

Ext.DOM.InsuredWindow = function(option) {
    if (option.checked) {
        var dialog = Ext.Window({
            url: 'form.edit.axa.product.php',
            width: parseInt(Ext.DOM.screen.availWidth - 300),
            height: parseInt(Ext.DOM.screen.availHeight - 200),
            name: 'WinEditInsured',
            param: {
                action: 'ShowData',
                CampaignId: Ext.Cmp('CampaignId').Encrypt(),
                InsuredId: Ext.BASE64.encode(option.value)
            }
        });

        dialog.popup();
    }
}

/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

Ext.DOM.PecahPolicy = function(PecahPolis) {
    if (PecahPolis == 1) {
        Ext.Cmp('InsuredPolicyNumber').disabled(false);
    } else {
        Ext.Cmp('InsuredPolicyNumber').disabled(true);
    }
}

/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

Ext.DOM.LoadSamePlan = function(opts) {
    var JSnum = (
        Ext.Ajax({
            url: '../class/class.SaveAxa.php',
            method: 'GET',
            param: {
                action: '_get_same_plan',
                PolicyNum: opts.value
            }
        }).json()
    );

    if (JSnum.result) {
        Ext.Cmp('InsuredPayMode').setValue(JSnum.paymode);
        Ext.Cmp('InsuredPayMode').disabled(true);
        Ext.Cmp('InsuredPlanType').setValue(JSnum.plan);
        Ext.Cmp('InsuredPlanType').disabled(true);
        Ext.DOM.ClearInsured();
    } else {
        Ext.Cmp('InsuredPayMode').setValue('');
        Ext.Cmp('InsuredPayMode').disabled(false);
        Ext.Cmp('InsuredPlanType').setValue('');
        Ext.Cmp('InsuredPlanType').disabled(false);
        Ext.DOM.ClearInsured();
    }
}

Ext.DOM.getPremi = function(opts) {
    if (Ext.Cmp('ProductId').empty()) {
        Ext.Msg("Product ID is Empty").Info();
        return false;
    } else if (Ext.Cmp('InsuredGroupPremi').empty()) {
        Ext.Msg("Group Premi is Empty").Info();
        return false;
    } else if (Ext.Cmp('InsuredAge').empty()) {
        Ext.Msg("Age is Empty").Info();
        return false;
    }// else if (Ext.Cmp('InsuredAge').getValue() == 0) {
        // Ext.Msg("Age is Zero").Info();
        // return false;
    // }
    //else if( Ext.Cmp('InsuredPayMode').empty() ){ Ext.Msg("Payment Mode").Info(); return false; }
    //else if( Ext.Cmp('InsuredPlanType').empty() ){ Ext.Msg("Product Plan").Info(); return false; }
    else {
        var JSnum = (
            Ext.Ajax({
                url: '../class/class.SaveAxa.php',
                method: 'GET',
                param: {
                    action: '_get_premi',
                    PlanTypeId: Ext.Cmp('InsuredPlanType').getValue(),
                    PersonalAge: Ext.Cmp('InsuredAge').getValue(),
                    PayModeId: Ext.Cmp('InsuredPayMode').getValue(),
                    ProductId: Ext.Cmp('ProductId').getValue(),
                    GroupPremi: Ext.Cmp('InsuredGroupPremi').getValue(),
					InsuredGenderId: Ext.Cmp('InsuredGenderId').getValue()
                }
            }).json()
        );

        Ext.Cmp('InsuredPremi').setValue(JSnum.personal_premi);
        Ext.Cmp('InsuredPremi').disabled(true);
    }
};

/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
Ext.DOM.Percentage = function() {
    var _box = Ext.Cmp('Benefeciery').getValue(),
        _tot = 0,
        _percent = 100,
        _percent_personal = 0;
    if (_box.length != 0) {
        _tot = parseInt(_percent) / parseInt(_box.length);

        for (var a in _box) {
            Ext.Cmp('BenefPercentage_' + _box[a]).setValue(_tot.toFixed(2));
        }
    }
};

/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

Ext.DOM.FormBenefiecery = function(checkbox, p) {
    if (checkbox.checked) {
        for (var i in Ext.DOM._benefiecery.field) {
            Ext.Cmp(Ext.DOM._benefiecery.field[i] + "_" + p).disabled(false);
            Ext.Cmp(Ext.DOM._benefiecery.field[i] + "_" + p).setValue('');
        }
    } else {
        for (var i in Ext.DOM._benefiecery.field) {
            Ext.Cmp(Ext.DOM._benefiecery.field[i] + "_" + p).disabled(true);
            Ext.Cmp(Ext.DOM._benefiecery.field[i] + "_" + p).setValue('');
        }
    }

    // calculation
    Ext.DOM.Percentage();
};

Ext.DOM.CopyData = function() {
    Ext.Ajax({
		url: '../class/class.SaveAxa.php',
		method: 'GET',
		param: {
			action: '_get_payer_data',
			CustomerId: Ext.Cmp('CustomerId').getValue()
		},
		ERROR: function(e) {
			var ERR = JSON.parse(e.target.responseText),
				p = 0;
			if (ERR) {
				for (var p in ERR) {
					Ext.Cmp(p).setValue(ERR[p]);
				}
			}
			// Ext.DOM.ValidPrefix();
			// Ext.DOM.ValidExpired();
		}
	}).post()
}

/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

Ext.DOM.CreatePolicyNumber = function(PolicyNumber) {
    var PecahPolicy = parseInt(Ext.Cmp('PecahPolicy').getValue());
    Ext.Ajax({
        url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_policy',
            ProductId: Ext.Cmp('ProductId').getValue(),
            CustomerId: Ext.Cmp('CustomerId').getValue(),
            SplitPolis: Ext.Cmp('PecahPolicy').getValue()
        }
    }).load('policy_number');
    Ext.DOM.ResetInsured();
    Ext.Cmp('InsuredPolicyNumber').listener({
        'onChange': function(e) {
            Ext.DOM.ClearInsured();
			Ext.DOM.benefInsured();
        }
    });
    if (PecahPolicy == 1) {
        Ext.Cmp('InsuredPolicyNumber').disabled(false);
    } else {
        Ext.Cmp('InsuredPolicyNumber').disabled(true);
    }
}

Ext.DOM.LoadPayMode = function() {
    var PecahPolicy = parseInt(Ext.Cmp('PecahPolicy').getValue());
    Ext.Ajax({
        url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_pay_mode',
            ProductId: Ext.Cmp('ProductId').getValue()
        }
    }).load('pay_plan');
	
	Ext.Cmp('InsuredPlanType').listener({
        'onChange': function(e) {
			Ext.DOM.benefInsured();
        }
    });
}

Ext.DOM.LoadPlanType = function() {
    var PecahPolicy = parseInt(Ext.Cmp('PecahPolicy').getValue());
    Ext.Ajax({
        url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_plan_type',
            ProductId: Ext.Cmp('ProductId').getValue()
        }
    }).load('plan_type');
	
	Ext.Cmp('InsuredPlanType').listener({
        'onChange': function(e) {
			Ext.DOM.benefInsured();
        }
    });
}

Ext.DOM.LoadGroupPremi = function() {
    var PecahPolicy = parseInt(Ext.Cmp('PecahPolicy').getValue());
    Ext.Ajax({
        url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_group_premi',
            ProductId: Ext.Cmp('ProductId').getValue()
        }
    }).load('group_premi');
}

/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

Ext.DOM.getSplitProduct = function(opts) {
    Ext.Ajax({
        url: '../class/class.SaveAxa.php',
        method: 'GET',
        param: {
            action: '_get_split',
            ProductId: opts.value
        },
        ERROR: function(e) {
            var ERR = JSON.parse(e.target.responseText);
			Ext.Cmp('categorycode').setValue(ERR.category);
            if (ERR.success && ERR.pecah != null) {
                if (ERR.pecah.toUpperCase() == 'ONE-TO-ONE') {
                    Ext.Cmp('PecahPolicy').disabled(true);
                    Ext.Cmp('PecahPolicy').setValue('1');
					Ext.DOM.CreatePolicyNumber();
                } else {
                    Ext.Cmp('PecahPolicy').disabled(true);
                    Ext.Cmp('PecahPolicy').setValue('0');
                }
            } else {
                Ext.Cmp('PecahPolicy').disabled(true);
                Ext.Cmp('PecahPolicy').setValue('');
            }
            Ext.DOM.CreatePolicyNumber();
        }
    }).post();

};

/* @ def 	:  _get_result_spouse 
 *
 * @ triger : _get_result_spouse Policy
 * @ params : jika terjadi pecah polis
 */
Ext.DOM._get_result_payers = function() {
    if (Ext.Cmp('PayerFirstName').empty()) {
        alert('PayerFirstName');
        next_process = 0;
    } else if (Ext.Cmp('PayerGenderId').empty()) {
        alert('PayerGenderId');
        next_process = 0;
    } 
    else if (Ext.Cmp('PayerPOB').empty()) {
        alert('PayerPOB');
        next_process = 0;
    } 

    else if (Ext.Cmp('PayerAddressLine1').empty()) {
        alert('PayerAddressLine1');
        next_process = 0;
    } else if (Ext.Cmp('PayerMobilePhoneNum').empty() && Ext.Cmp('PayerHomePhoneNum').empty() && Ext.Cmp('PayerOfficePhoneNum').empty()) {
        alert('PayerMobilePhoneNum');
        next_process = 0;
    } else if (Ext.Cmp('PayerCity').empty()) {
        alert('PayerCity');
        next_process = 0;
    // } else if (Ext.Cmp('PayerZipCode').empty()) {
        // alert('PayerZipCode');
        // next_process = 0;
    // } 
	}
    else if (Ext.Cmp('PayerProvinceId').empty()) {
        alert('PayerProvinceId');
        next_process = 0;
    } 
    
    // else if (Ext.Cmp('PayersBankId').empty()) {
        // alert('PayersBankId');
        // next_process = 0;
    // } 
	// else if (Ext.Cmp('CreditCardTypeId').empty()) {
        // alert('Card Type');
        // next_process = 0;
    // }
	else {
        next_process = 1;
    }
    return next_process;
}

Ext.DOM._get_result_holder = function() {
	
	if (Ext.Cmp('HolderFirstName').empty()) {
        alert('HolderFirstName');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderGenderId').empty()) {
        alert('HolderGenderId');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderPOB').empty()) {
        alert('HolderPOB');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderDOB').empty()) {
        alert('HolderDOB');
        next_process = 0;
    }
	// else if (Ext.Cmp('HolderPosition').empty()) {
        // alert('HolderPosition');
        // next_process = 0;
    // }
	else if (Ext.Cmp('PayerMobilePhoneNum').empty() && Ext.Cmp('PayerHomePhoneNum').empty() && Ext.Cmp('PayerOfficePhoneNum').empty()) {
        alert('PayerMobilePhoneNum');
        next_process = 0;
    }
	// else if (Ext.Cmp('HolderOccupation').empty()) {
        // alert('HolderOccupation');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderIncome').empty()) {
        // alert('HolderIncome');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderCompany').empty()) {
        // alert('HolderCompany');
        // next_process = 0;
    // }
	else if (Ext.Cmp('HolderMobilePhoneNum').empty()) {
        alert('HolderMobilePhoneNum');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderMaritalStatus').empty()) {
        alert('HolderMaritalStatus');
        next_process = 0;
    }
	// else if (Ext.Cmp('HolderIdentificationTypeId').empty()) {
        // alert('HolderIdentificationTypeId');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderIdentificationNum').empty()) {
        // alert('HolderIdentificationNum');
        // next_process = 0;
    // }
	else if (Ext.Cmp('HolderRelationshipTypeId').empty()) {
        alert('HolderRelationshipTypeId');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderAddrType').empty()) {
        alert('HolderAddrType');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderAddressLine1').empty()) {
        alert('HolderAddressLine1');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderProvinceId').empty()) {
        alert('HolderProvinceId');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderCity').empty()) {
        alert('HolderCity');
        next_process = 0;
    }
	else if (Ext.Cmp('HoldersBankId').empty()) {
        alert('HoldersBankId');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderBankBranch').empty()) {
        alert('HolderBankBranch');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderCreditCardNum').empty()) {
        alert('HolderCreditCardNum');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderZipCode').empty()) {
        alert('HolderZipCode');
        next_process = 0;
    }
	else {
        next_process = 1;
    }
    return next_process;
}

Ext.DOM._get_result_insured = function() {
    next_process = 0;
    for (var i in Ext.DOM.Insured.field) {
        if (Ext.Cmp(i).empty() && Ext.DOM.Insured.field[i].keys) {
            Ext.Msg(Ext.DOM.Insured.field[i].warn).Info();
            next_process = 0
            return false;
        } else {
            next_process = 1;
        }
    }

    return next_process;
}

Ext.DOM.ValidPercentage = function()
{
	var _conds = false;
	var _box = Ext.Cmp('Benefeciery').getValue(),
		_tot = 0;
       
    
	if (_box.length != 0) {
		for (var a in _box) {
            _tot += parseInt(Ext.Cmp('BenefPercentage_' + _box[a]).getValue());
        }
    }
	
	if(_tot != 100)
	{
		alert("Beneficiary Percentage, just "+_tot+"% !");
		return false;
	}
	else{
		_conds = true;
	}
	
	return _conds;
}

Ext.DOM.ValidasiBenef = function()
{
	var conds = false;
	
	var JSnum = (
        Ext.Ajax({
            url: '../class/class.SaveAxa.php',
            method: 'GET',
            param: {
                action: '_get_product_benef',
                ProductId: Ext.Cmp('ProductId').getValue()
            }
        }).json()
    );
	
	if(JSnum.result)
	{
		if(JSnum.value)
		{
			if(Ext.Cmp("Benefeciery").Checked())
			{
				var _box = Ext.Cmp('Benefeciery').getValue();
                for (var b in _box){
                                
                   if(Ext.Cmp('BenefFirstName_' + _box[b]).getValue() == ""){
                             conds = false;
                   }else if(Ext.DOM.ValidPercentage())
				   {
					   conds = true;
					}else{
						conds = true;
					}
                               
               }
				/*
				if(Ext.DOM.ValidPercentage())
				{
					conds = true;
				}
				*/
			}
		}
		else{
			conds = true;
		}
	}
	
	return conds;
}

/***
** validation insured age
***/
Ext.DOM.validInsuredAge  = function(){
	var InsuredDOB = Ext.Cmp('InsuredDOB').getValue();
	if (InsuredDOB != '')
	{
		 _c = InsuredDOB.split('-'),
         _d = _c[2] + '-' + _c[1] + '-' + _c[0];
		if( Ext.Cmp('ProductId').getValue() != '' )
		{
					var JSnum = (
						Ext.Ajax({
							url: '../class/class.SaveAxa.php',
							method: 'GET',
							param: {
								action: '_get_age',
								ProductId: Ext.Cmp("ProductId").getValue(),
								GroupPremi: Ext.Cmp("InsuredGroupPremi").getValue(),
								DOB: _d.trim()
							}
						}).json()
					);
				
					if (JSnum.success) {
						//Ext.Cmp('InsuredAge').setValue(JSnum.personal_age);
						return true;
					} 
					else {
						Ext.Msg(JSnum.Error).Error();
						return false;
					}
		}
		else{
			alert('Please, choose product!');
			return false;
		}
	}
	else
	{
		return false;
	}
}

/*** validasi survey **/
Ext.DOM.ValidSurvey = function()
{
	var red_mark=0;
	// console.log(Mandat_SurveyQuest);
	// console.log(Mandat_SurveyAns);
	for(var i in Mandat_SurveyQuest )
	{
			if(typeof Mandat_SurveyAns[i] === 'undefined') 
			{
				// Ext.Cmp(Ext.DOM._benefieceryAdd.field[2]+"_"+p).empty()
				// alert('survey_'+Mandat_SurveyQuest[i]);
				// alert(Ext.Cmp('survey_'+Mandat_SurveyQuest[i]).getValue());
				// alert(Ext.Cmp('survey_'+Mandat_SurveyQuest[i]).getValue());
				if(  Ext.Cmp('survey_'+Mandat_SurveyQuest[i]).getValue()=="" )
				{
					// alert("Please answer question");
					$("#ans_valid_"+Mandat_SurveyQuest[i]).css({"border":"2px solid #FF0000"});
					red_mark++;
				}
				else
				{
					$("#ans_valid_"+Mandat_SurveyQuest[i]).css({"border":"0px solid #000000"});
				}
			}
			else 
			{
				var checkans="";
				for(var j in Mandat_SurveyAns[i] )
				{
					checkans = checkans + Ext.Cmp('survey_'+Mandat_SurveyQuest[i]+'_'+Mandat_SurveyAns[i][j]).getValue();	
				}
				if ( checkans =="" )
				{
					// alert("Please answer question");
					$("#ans_valid_"+Mandat_SurveyQuest[i]).css({"border":"2px solid #FF0000"});
					red_mark++;
				}
				else
				{
					$("#ans_valid_"+Mandat_SurveyQuest[i]).css({"border":"0px solid #000000"});
				}
			}
			// alert(typeof Mandat_SurveyAns[i]);
			
	}
	// alert(red_mark);
	if(red_mark>0)
	{
		
		return true;
	}
	else
	{
		return false;
	}
}
/** validasi uw kosong **/
Ext.DOM.UWEmpty = function()
{
	var empty = false;
	var question_uw =(Ext.Ajax({ 
				url 	: '../class/class.SaveAxa.php', 
				method :'GET', 
				param 	: { 
					action	: '_getUWQuestion',
					ProductId : Ext.Cmp('ProductId').getValue()
					}
				}).json() );
				// console.log(question_uw);
	for ( var i in question_uw )
	{
		// console.log(Ext.Cmp('survey_'+i).empty());
		// console.log('survey_'+i);
		if(Ext.Cmp('survey_'+i).empty())
		{
			empty = true;
		}
		else{
			empty = false;
			break;
		}
			
	}
	return empty;
	
}
Ext.DOM.validPayMethod = function()
{
	var prod_category_code = Ext.Cmp('categorycode').getValue();
	var error_found = false;
	if( prod_category_code == "FPA" )
	{
		var PayType = Ext.Cmp('PayerPaymentType').getValue();
		
		if (PayType==""){
			alert("Payer Payment Type Is empty");
			error_found = true;
		}
		error_found = window[PayMethod.validasi[PayType]]();
	}
	else if( prod_category_code == "APE" )
	{
		if(Ext.Cmp("isXsell").getValue() == "1")
		{
			// var a = [
					// {"cmp" : "CreditCardTypeId", "msg" : "Card Type is empty"},
					// {"cmp" : "PayersBankId", "msg" : "Payer Bank is empty"}
				// ];
				var a = [
					{"cmp" : "CreditCardTypeId", "msg" : "Card Type is empty"},
					{"cmp" : "PayersBankId", "msg" : "Payer Bank is empty"}
				];
			for (var i in a) {
				if (Ext.Cmp(a[i].cmp).empty()) {
					alert(a[i].msg);
					error_found = true;
					break;
				}
			}
		}
		else
		{
			
			if($('#digit_arr').length == 0){
				alert("Please check digit first");
				error_found = true;
			}
			var IvrPayMethode = Ext.Cmp('IvrPayMethod').getValue();
			if(IvrPayMethode != "")
			{
				if( PayMethod.bool_bank[IvrPayMethode] == "1" )
				{
					if(Ext.Cmp("IvrBankId").empty())
					{
						alert("Please select IVR Bank");
						error_found = true;
					}
				}
			}
			
		}
	}
	return error_found;
}
function valid_cc_form()
{
	var error_found = false;
	var prod_category_code = Ext.Cmp('categorycode').getValue();
	if( prod_category_code == "FPA" )
	{
		var a = [
					{"cmp" : "PayerCreditCardNum", "msg" : "Payer Credit Card Number is empty"},
					{"cmp" : "PayerCreditCardExpDate", "msg" : "Expiration Date is empty"},
					{"cmp" : "PayersBankId", "msg" : "PayersBankId"},
					{"cmp" : "CreditCardTypeId", "msg" : "Card Type is empty"},
				];

		for (var i in a) {
			// console.log(a[i]);
			console.log(a[i].msg);
			if (Ext.Cmp(a[i].cmp).empty()) {
				alert(a[i].msg);
				error_found = true;
			}
		}
		// if (Ext.Cmp('PayerCreditCardNum').empty()) {
			// alert('Payer Credit Card Number is empty');
			// error_found = true;
		// }
		// if (Ext.Cmp('PayerCreditCardExpDate').empty()) {
			// alert('Expiration Date is empty');
			// error_found = true;
		// }	
		if(!Ext.DOM.Variable.prefix){
			alert("Please, input valid card number!");
			error_found = true;
		}
		if(!Ext.DOM.Variable.expire){
			alert("Credit card has expired!");
			error_found = true;
		}
		// if (Ext.Cmp('PayersBankId').empty()) {
			// alert('PayersBankId');
			// error_found = true;
		// } 
		// if (Ext.Cmp('CreditCardTypeId').empty()) {
			// alert('Card Type');
			// error_found = true;
		// }
	}
	// else if (prod_category_code == "APE")
	// {
		// if ( Ext.Cmp('PayerCreditCardNum').empty() && Ext.Cmp('PayerCreditCardExpDate').empty() ) {
			// alert('Credit Card Is empty');
			// error_found = true;
		// }
	// }
	return error_found;
}
function valid_saving_form()
{
	var error_found = false;
	var prod_category_code = Ext.Cmp('categorycode').getValue();
	if( prod_category_code == "FPA" )
	{
	
		if (Ext.Cmp('SavingAccount').empty()) {
			alert('Saving Account is empty');
			error_found = true;
		}
		if (Ext.Cmp('PayersBankId').empty()) {
			alert('PayersBankId');
			error_found = true;
		} 
		if (Ext.Cmp('CreditCardTypeId').empty()) {
			alert('Card Type');
			error_found = true;
		}
	}
	return error_found;
}
function reset_cc_form()
{
	var attPayment = ["PayerCreditCardNum","PayerCreditCardExpDate"];
	var validsymbol = ["error_message_html","error_message_exp"];
	for (var i in attPayment) {
		Ext.Cmp(attPayment[i]).setValue("");
	}
	for (var j in validsymbol) {
		Ext.Cmp(validsymbol[j]).setText("<img src=\"../gambar/icon/delete.png\">");
	}
	Ext.DOM.Variable.expire = 0;
	Ext.DOM.Variable.prefix = 0;
}
function reset_saving_form()
{
	var attPayment = ["SavingAccount"];
	var validsymbol = ["error_message_html"];
	for (var i in attPayment) {
		Ext.Cmp(attPayment[i]).setValue("");
	}
	for (var j in validsymbol) {
		Ext.Cmp(validsymbol[j]).setText("<img src=\"../gambar/icon/delete.png\">");
	}
}
/*** validasi rule ***/
 Ext.DOM.ValidRule = function ()
 {
	var callback = false;
	var question_valid = new Array();
	var valid_answer =(Ext.Ajax({ 
				url 	: '../class/class.SaveAxa.php', 
				method :'GET', 
				param 	: { 
					action	: '_getValidAnswer',
					ProductId : Ext.Cmp('ProductId').getValue()
					}
				}).json() );
	var except_question =(Ext.Ajax({ 
				url 	: '../class/class.SaveAxa.php', 
				method :'GET', 
				param 	: { 
					action	: '_getExceptQuestion',
					ProductId : Ext.Cmp('ProductId').getValue()
					}
				}).json() );
	// console.log(except_question);
	for ( var i in valid_answer.question )
	{
		// console.log("Question ke " );
		// console.log(i);
		// var questionid = valid_answer.question[i];
		
		// console.log(except_question[i]);
		// console.log("jawaban valid untuk pertanyaan "+i);
		
		// console.log(valid_answer.answer[i]);
		
		var a = valid_answer.answer[i];
		// console.log( (a));
		var all_ans = Ext.Cmp('survey_'+i).getValue();
		
		// console.log("Jawaban dari pertanyaan "+ i);
		// console.log( (all_ans) );
		
		// console.log("type variabel Jawaban dari pertanyaan "+ i);
		// console.log( typeof(all_ans) );
		
		// console.log("Panjang objek jawaban dari pertanyaan " + i);
		// console.log( typeof(all_ans) );
		if(all_ans.length==1)
		{
			var ans = String(all_ans);
		// console.log( a.indexOf( ans ) );
			if( a.indexOf(ans) == -1 )
			{
				// question_valid[i]=false;
				 if(typeof except_question[i] !='undefined')
				 {
					// console.log('survey_'+except_question[i]);
					// console.log(Ext.Cmp('survey_'+except_question[i]).getValue());
					var ex_select_user = Ext.Cmp('survey_'+except_question[i]).getValue();
					// console.log(ex_select_user.length);
					if(ex_select_user.length > 0 )
					{
						var b = valid_answer.answer[except_question[i]];
						for ( var s in ex_select_user )
						{
							// console.log( String(ex_select_user[s]) );
							// console.log( typeof ( String(ex_select_user[s]) ) );
							// console.log( b.indexOf(String(ex_select_user[s])) );
							if(b.indexOf(String(ex_select_user[s])) == -1)
							{
								
								callback=true;
								break;
									// console.log(callback);
							}
							else
							{
								callback=false;
								// console.log(callback);
							}
							
						}
					}
					else
					{
						callback=true;
						break;
					}
					
				 }
				 else
				 {
					callback=true;
					break;
				 }
			}
			else
			{
				// console.log(true);
				// question_valid[i]=true;
				// console.log(i);
				// question_valid.push={i:[true]};
				callback=false;
				// break;
			}
		// alert(valid_answer.question[i]);
		// for ( var j in valid_answer.answer[i] )
		// {
			// console.log(valid_answer.answer[i][j]);
		// }
		// alert('survey_'+
		}
		else
		{
			for ( var multi in all_ans )
			{
				
				if(a.indexOf(String(all_ans[multi])) == -1)
				{
					
					callback=true;
					break;
						// console.log(callback);
				}
				else
				{
					callback=false;
					// console.log(callback);
				}
				
			}
		}
	}
	return callback;
 }
/* @ def 	:  SavePolis 
 *
 * @ triger : SavePolis Policy
 * @ params : jika terjadi pecah polis
 */
Ext.DOM.SavePolis = function() {
	// Ext.DOM.validPayMethod(); aaaa
	// Ext.DOM.MoveToText(); aaaa
    // if (Ext.DOM.validInsured()) {
		// if(Ext.DOM.ValidasiBenef())
		// {
			var VAR_POST_DATA = [];
			VAR_POST_DATA['action'] = '_savePolis';
			VAR_POST_DATA['BenefBox'] = Ext.Cmp('Benefeciery').getValue();

			// if (Ext.Cmp('ProductId').empty()) {
				// alert("Product is empty ");
				// return false;
			// }
			
			if (!Ext.DOM._get_result_holder()) {
				return false;
			}
			
			// else if (Ext.Cmp('PayerEmail').empty()) {
				// alert("Please, input Email address! ");
				// return false;
			// }
			else if (Ext.Cmp("PayerDOB").getValue()=='00-00-0000') {
				alert("Please, input Valid DOB! ");
				return false;
			}
			// else if ( Ext.DOM.ValidSurvey() ) {
				// alert("Please answer survey or underwriting (red box area)");
				// return false;
			// } 
			// else if (  Ext.DOM.ValidRule() ) {
				// alert("Rule is not Valid, Can\'t Create Policy");
				// return false;
			// }
			
			// else if (Ext.DOM.UWEmpty())
			// {
				// alert("Underwriting is empty");
				// return false;
			// }
			else if (!Ext.DOM._get_result_payers()) {
				return false;
			} else if (!Ext.DOM._get_result_insured()) {
				return false;
			} 
			// else if ( Ext.DOM.validPayMethod() ){
				// return false;
			// }
			// else {
				// if (Ext.DOM.validInsuredAge()) 
				// {
					Ext.Ajax({
						url: '../class/class.SaveAxa.php',
						method: 'POST',
						param: (Ext.Join(
							new Array(
								VAR_POST_DATA,
								Ext.Serialize('form_data_payer').getElement(),
								Ext.Serialize('form_data_holder').getElement(),
								Ext.Serialize('form_data_product').getElement(),
								Ext.Serialize('form_survey').getElement(),
								Ext.Serialize('form_uw').getElement(),
								Ext.Serialize('form_payment_ivr').getElement(),
								Ext.Serialize('form_data_insured').getElement(), (Ext.Cmp('Benefeciery').Checked() ? Ext.Serialize('form_data_benefiecery').getElement() : new Array())
							)
						).object()),
						ERROR: function(e) {
							var ERR = JSON.parse(e.target.responseText),
								message = '';
							if (ERR.success == 1) {
								alert("Sucess, Create Polis , with number polis :\n" + ERR.polis);
								
								Ext.DOM.CreatePolicyNumber(ERR.polis);
								Ext.Cmp("ProductId").setValue('');
								Ext.Cmp("PecahPolicy").setValue('');
								Ext.DOM.ResetInsured();
								Ext.Cmp("CopyDataInsured").setUnchecked();
								Ext.Cmp("CopyDataInsured").disabled(true);
								Ext.Cmp("InsuredPolicyNumber").setValue('new');
								Ext.Cmp("InsuredGroupPremi").setValue('');
								$( "#tabs" ).tabs( "option", "selected", 0 );
								$( "#tabs" ).tabs( "option", "disabled", [0,1,2,3,4,5,6,7]);
								Ext.DOM.benefInsured();
								Ext.Cmp('ivr_list').setText("");
								Ext.Cmp('IvrPayMethod').setValue("");
							} else if (ERR.success == 2) {
								alert("Info, Polis alerdy exist , with number polis :\n" + ERR.polis);
								
								Ext.DOM.CreatePolicyNumber(ERR.polis);
								Ext.Cmp("ProductId").setValue('');
								Ext.Cmp("PecahPolicy").setValue('');
								Ext.DOM.ResetInsured();
								Ext.Cmp("CopyDataInsured").setUnchecked();
								Ext.Cmp("CopyDataInsured").disabled(true);
								Ext.Cmp("InsuredPolicyNumber").setValue('new');
								Ext.Cmp("InsuredGroupPremi").setValue('');
								$( "#tabs" ).tabs( "option", "selected", 0 );
								$( "#tabs" ).tabs( "option", "disabled", [0,1,2,3,4,5,6,7]);
								Ext.DOM.benefInsured();
								Ext.Cmp('ivr_list').setText("");
								Ext.Cmp('IvrPayMethod').setValue("");
							} else {
								alert("Failed, Create Polis. please try again !");
							}
						}

					}).post();
				// }
			// }
		// }
		// else{
			// alert('Input Beneficiary not complete!');
			// return false;
		// }
    // } else {
        // alert("Premium group "+(Ext.Cmp("InsuredGroupPremi").getValue()==2?'Main Insured':Ext.Cmp("InsuredGroupPremi").getValue()==3?'Spouse':'Dependent')+", already exist!");
        // return false;
    // }
};

Ext.DOM.splitintoivr = function(){
	var CustomerId = Ext.Cmp("CustomerId").getValue();
        var port = Ext.Cmp('IvrPayMethod').getValue();
        if (port != ""){
            window.opener.document.ctiapplet.callSplitToIVR(String(port), '3800', String(CustomerId));
			// console.log(String(port)+", "+"3800"+", "+String(CustomerId))
        }
		else{
            alert("Failed send to ivr");
        }
	//window.opener.document.ctiapplet.callSplitToIVR('3900', '3800', String(CustomerId));
	// var vervar = window.opener.document.ctiapplet.getVersion();
	// alert(CustomerId);
	// alert(port);
};

// Ext.DOM.MoveToText = function(){
    // var method = Ext.Cmp('IvrPayMethod').getValue();
	// var digitivr = Ext.Cmp('digit_arr').getValue();
	// if(method != "")
	// {
		// if (method == "3900"){
			// var NumCard = (
							// Ext.Ajax({
								// url: '../class/class.SaveAxa.php',
								// method: 'GET',
								// param: {
									// action: 'move_cc',
									// CustomerId: Ext.Cmp("CustomerId").getValue(),
									// DigitId : digitivr
								// }
							// }).json()
						// );
			// console.log(NumCard);
			// Ext.Cmp('PayerCreditCardNum').setValue(NumCard.card_number);
			// Ext.Cmp('PayerCreditCardExpDate').setValue(NumCard.Expire);
			// Ext.Cmp('SavingAccount').setValue("");
		// }else if (method == "3901"){
			
			// var SavingCard = (
							// Ext.Ajax({
								// url: '../class/class.SaveAxa.php',
								// method: 'GET',
								// param: {
									// action: 'move_saving',
									// CustomerId: Ext.Cmp("CustomerId").getValue(),
									// DigitId : digitivr
								// }
							// }).json()
						// );
			// Ext.Cmp('SavingAccount').setValue(SavingCard.card_number);
			// Ext.Cmp('PayerCreditCardNum').setValue("");
			// Ext.Cmp('PayerCreditCardExpDate').setValue("");
			// console.log(SavingCard);
		// }
		// Ext.Cmp('PayerPaymentType').setValue(PayMethod.ivr[method]);
	// }
// };

Ext.DOM.getcustomercc = function(){
    /*
	Ext.Ajax({
		url: '../class/class.SaveAxa.php',
		method: 'POST',
		param: {
			action: 'customer_cc_checking',
            CustomerId : Ext.Cmp("CustomerId").getValue()
		},
		ERROR: function(e) {
			var ERR = JSON.parse(e.target.responseText),
				message = '';
			if (ERR.success == 1){
				// Ext.Cmp('error_message_html').setText(ERR.img);
				Ext.Cmp('error_messagecc_html').setText('<img src="../gambar/icon/accept.png">');
				// Ext.Cmp("hiddennocc").setValue(ERR.nocc);
				// Ext.DOM.Variable.prefixivr = ERR.nocc;
				// Ext.DOM.Variable.prefixivrcopy = ERR.nocc;
				alert('Credit Card Information is Valid');
			}else{
				Ext.Cmp('error_messagecc_html').setText(ERR.img);
				alert('Credit Card Information is Not Complete !');
			}
		}
	}).post();
        */
       // Ext.DOM.MoveToText();
};

Ext.DOM.check_digit_valid = function()
{
	var ivrpay = Ext.Cmp('IvrPayMethod').getValue();
	Ext.Cmp("ivr_list").setText("");
	if(ivrpay != "")
	{
		// var jsNum = (
			// Ext.Ajax({
				// url: '../class/class.SaveAxa.php',
				// method: 'GET',
				// param: {
					// action: '_check_digit',
					// CustomerId: Ext.Cmp("CustomerId").getValue(),
					// PayMethodId: PayMethod.ivr[ivrpay]
				// }
			// }).json()
		// );
		// Ext.Cmp('digit_message_html').setText(jsNum.img);
		// Ext.Ajax({
			// url: '../class/class.SaveAxa.php',
			// method: 'GET',
			// param: {
				// action: '_check_digit',
				// CustomerId: Ext.Cmp("CustomerId").getValue(),
				// PayMethodId: PayMethod.ivr[ivrpay]
			// }
		// }).load("ivr_list");
		if (ivrpay == "3900"){
			// var NumCard = (
							// Ext.Ajax({
								// url: '../class/class.SaveAxa.php',
								// method: 'GET',
								// param: {
									// action: '_get_card_numbercc',
									// CustomerId: Ext.Cmp("CustomerId").getValue(),
									// PayMethodId: PayMethod.ivr[ivrpay]
								// }
							// }).json()
						// );
			Ext.Ajax({
				url: '../class/class.SaveAxa.php',
				method: 'GET',
				param: {
					action: 'show_card_numbercc',
					CustomerId: Ext.Cmp("CustomerId").getValue(),
					PayMethodId: PayMethod.ivr[ivrpay]
				}
			}).load("ivr_list");
		}else if (ivrpay == "3901"){
			
			// var SavingCard = (
							// Ext.Ajax({
								// url: '../class/class.SaveAxa.php',
								// method: 'GET',
								// param: {
									// action: '_get_card_numbersaving',
									// CustomerId: Ext.Cmp("CustomerId").getValue(),
									// PayMethodId: PayMethod.ivr[ivrpay]
								// }
							// }).json()
						// );
			Ext.Ajax({
				url: '../class/class.SaveAxa.php',
				method: 'GET',
				param: {
					action: 'show_card_numbersaving',
					CustomerId: Ext.Cmp("CustomerId").getValue(),
					PayMethodId: PayMethod.ivr[ivrpay]
				}
			}).load("ivr_list");
		}
		//console.log($('#digit_arr').length);
		// if($('#digit_arr').length != 0){
			// alert("digit_arr was found");
		// }
		
		// console.log(Ext.Cmp('digit_arr').getValue());
		
	}
	else
	{
		alert("Please choose IVR Payment Methode");
	}
	
	// console.log(PayMethod.ivr[ivrpay]);
	// console.log(ivrpay);
	
};

Ext.DOM.CopyDataPayer = function( cbXsell )
{
	// console.log(cbXsell.value);
	// console.log(Ext.Cmp("cbxDataPayer").getValue().toString());
	// console.log(cbXsell.checked);
	if( cbXsell.checked )
	{
		Ext.Cmp("isXsell").setValue("1");
		Ext.Cmp('ivr_list').setText("");
		Ext.Cmp('xsellinfo').setText(Ext.Cmp("PayerXsellbank").getValue());
		Ext.Cmp('IvrPayMethod').setValue("");
		$( "#tabs" ).tabs( "option", "disabled", [7]);
		
		// Ext.Cmp('PayerPaymentType').disabled(false);
		// Ext.Cmp("PayerPaymentType").setValue('');
		Ext.Cmp('CreditCardTypeId').disabled(false);
		Ext.Cmp("CreditCardTypeId").setValue('');
		Ext.Cmp('PayersBankId').disabled(false);
		Ext.Cmp("PayersBankId").setValue('');
		Ext.DOM.loadCardType();
	}
	else
	{
		Ext.Cmp("isXsell").setValue("0");
		Ext.Cmp('xsellinfo').setText("");
		$( "#tabs" ).tabs( "option", "disabled", []);
		// Ext.Cmp('PayerPaymentType').disabled(true);
		// Ext.Cmp("PayerPaymentType").setValue('');
		Ext.Cmp('CreditCardTypeId').disabled(true);
		Ext.Cmp("CreditCardTypeId").setValue('');
		Ext.Cmp('PayersBankId').disabled(true);
		Ext.Cmp("PayersBankId").setValue('');
	}
	

};

Ext.DOM.loadCardType = function ()
{
	var PayType = Ext.Cmp('PayerPaymentType').getValue();
	Ext.Ajax({
		url: '../class/class.SaveAxa.php',
		method: 'GET',
		param: {
			action: '_card_type_pay',
			Pay_Type :PayType
		}
	}).load("dyn_card_type");
};

Ext.DOM.loadIvrBank = function ()
{
	var IvrPayMethode = Ext.Cmp('IvrPayMethod').getValue();
	// if( IvrPayMethode != '')
	// {
		Ext.Ajax({
			url: '../class/class.SaveAxa.php',
			method: 'GET',
			param: {
				action: '_load_ivr_bank',
				IvrPayMethode :PayMethod.ivr[IvrPayMethode]
			}
		}).load("ivr_bank");
	// }
	
};

// $( function() {
    // var availableTags = [
      // "ActionScript",
      // "AppleScript",
      // "Asp",
      // "BASIC",
      // "C",
      // "C++",
      // "Clojure",
      // "COBOL",
      // "ColdFusion",
      // "Erlang",
      // "Fortran",
      // "Groovy",
      // "Haskell",
      // "Java",
      // "JavaScript",
      // "Lisp",
      // "Perl",
      // "PHP",
      // "Python",
      // "Ruby",
      // "Scala",
      // "Scheme"
    // ];
    // $( "#PayerPOB" ).autocomplete({
      // source: availableTags
    // });
  // } );