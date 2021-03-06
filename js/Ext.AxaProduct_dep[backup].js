/* @ def 	:  define of Global dependent input if not followed !, Try on window object
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
// define dependent field
Ext.DOM._benefiecery = {
    field: [
        'BenefRelationshipTypeId',
        'BenefSalutationId',
        'BenefFirstName',
        'BenefLastName',
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
        InsuredAge: {
            keys: true,
            warn: 'Age is empty',
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
    $("#tabs").tabs();
	$( "#tabs" ).tabs( "option", "disabled", [0,1,2,3,4]);
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
						var JSnum = (
							Ext.Ajax({
								url: '../class/class.SaveAxa.php',
								method: 'GET',
								param: {
									action: '_get_age_payer',
									DOB: _d.trim()
								}
							}).json()
						);
						Ext.Cmp('PayerAge').setValue(JSnum.personal_age);
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
        Ext.Cmp('InsuredDOB').setValue(Ext.Cmp('PayerDOB').getValue()),
		Ext.Cmp('InsuredAge').setValue(Ext.Cmp('PayerAge').getValue()),
		
		Ext.Cmp('InsuredIdentificationTypeId').disabled(true),
        Ext.Cmp('InsuredIdentificationTypeId').disabled(true),
        Ext.Cmp('InsuredIdentificationNum').disabled(true),
        Ext.Cmp('InsuredSalutationId').disabled(true),
        Ext.Cmp('InsuredFirstName').disabled(true),
        Ext.Cmp('InsuredLastName').disabled(true),
        Ext.Cmp('InsuredGenderId').disabled(true),
        Ext.Cmp('InsuredDOB').disabled(true),
		Ext.Cmp('InsuredAge').disabled(true)
    ) : (
        Ext.Cmp('InsuredIdentificationTypeId').setValue(''),
        Ext.Cmp('InsuredIdentificationTypeId').setValue(''),
        Ext.Cmp('InsuredIdentificationNum').setValue(''),
        Ext.Cmp('InsuredSalutationId').setValue(''),
        Ext.Cmp('InsuredFirstName').setValue(''),
        Ext.Cmp('InsuredLastName').setValue(''),
        Ext.Cmp('InsuredGenderId').setValue(''),
        Ext.Cmp('InsuredDOB').setValue(''),
        Ext.Cmp('InsuredAge').setValue(''),
		
		Ext.Cmp('InsuredIdentificationTypeId').disabled(false),
        Ext.Cmp('InsuredIdentificationTypeId').disabled(false),
        Ext.Cmp('InsuredIdentificationNum').disabled(false),
        Ext.Cmp('InsuredSalutationId').disabled(false),
        Ext.Cmp('InsuredFirstName').disabled(false),
        Ext.Cmp('InsuredLastName').disabled(false),
        Ext.Cmp('InsuredGenderId').disabled(false),
        Ext.Cmp('InsuredDOB').disabled(false),
		Ext.Cmp('InsuredAge').disabled(false)
    ))
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
            //alert("asd")
			Ext.DOM.benefInsured();
			getPremi(Ext.Cmp('InsuredPlanType').getValue());
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
	
    Ext.Cmp('PayerCreditCardNum').listener({
        'onKeyup': function(e) {
            Ext.Set(e.currentTarget.id).IsNumber();
        }
    });
	
    Ext.Cmp('PayerFaxNum').listener({
        'onKeyup': function(e) {
            Ext.Set(e.currentTarget.id).IsNumber();
        }
    });
	
    Ext.Cmp('ProductId').listener({
        'onChange': function(e) {
			Ext.DOM.LoadPayMode();
            Ext.DOM.LoadPlanType();
			Ext.DOM.LoadGroupPremi();
			Ext.Cmp("CopyDataInsured").setUnchecked();
			Ext.Cmp("CopyDataInsured").disabled(true);
			Ext.DOM.Benefit();
			Ext.DOM.BenefActive();
			if(Ext.Cmp('ProductId').getValue())
			{
				$( "#tabs" ).tabs( "option", "disabled", []);
			}
			else{
				$( "#tabs" ).tabs( "option", "disabled", [0,1,2,3,4]);
			}
        }
    });
		
    Ext.Cmp('TRANSACTION').listener({
        'onClick': function(e) {
            Ext.DOM.Transaction();
        }
    });
	
	Ext.DOM.CopyData();
});

/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

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
            plan: Ext.Cmp("InsuredPlanType").getValue()
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
    }).load("Transaction")
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
                    GroupPremi: Ext.Cmp('InsuredGroupPremi').getValue()
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
            //alert("asd")
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
    } /*else if (Ext.Cmp('PayerDOB').empty()) {
        alert('PayerDOB');
        next_process = 0;
    }*/ else if (Ext.Cmp('PayerAddressLine1').empty()) {
        alert('PayerAddressLine1');
        next_process = 0;
    } else if (Ext.Cmp('PayerMobilePhoneNum').empty() && Ext.Cmp('PayerHomePhoneNum').empty() && Ext.Cmp('PayerOfficePhoneNum').empty()) {
        alert('PayerMobilePhoneNum');
        next_process = 0;
    } else if (Ext.Cmp('PayerCity').empty()) {
        alert('PayerCity');
        next_process = 0;
    } else if (Ext.Cmp('PayerZipCode').empty()) {
        alert('PayerZipCode');
        next_process = 0;
    } /*else if (Ext.Cmp('PayerProvinceId').empty()) {
        alert('PayerProvinceId');
        next_process = 0;
    }*/ else if (Ext.Cmp('PayerCreditCardNum').empty()) {
        alert('PayerCreditCardNum');
        next_process = 0;
    } else if (Ext.Cmp('PayersBankId').empty()) {
        alert('PayersBankId');
        next_process = 0;
    } else if (Ext.Cmp('PayerCreditCardExpDate').empty()) {
        alert('Expiration Date');
        next_process = 0;
    } else {
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
				if(Ext.DOM.ValidPercentage())
				{
					conds = true;
				}
			}
		}
		else{
			conds = true;
		}
	}
	
	return conds;
}
/* @ def 	:  SavePolis 
 *
 * @ triger : SavePolis Policy
 * @ params : jika terjadi pecah polis
 */
Ext.DOM.SavePolis = function() {
    if (Ext.DOM.validInsured()) {
		if(Ext.DOM.ValidasiBenef())
		{
			var VAR_POST_DATA = [];
			VAR_POST_DATA['action'] = '_savePolis';
			VAR_POST_DATA['BenefBox'] = Ext.Cmp('Benefeciery').getValue();

			if (Ext.Cmp('ProductId').empty()) {
				alert("Product is empty ");
				return false;
			} else if (!Ext.DOM._get_result_payers()) {
				return false;
			} else if (!Ext.DOM._get_result_insured()) {
				return false;
			} else {
				Ext.Ajax({
					url: '../class/class.SaveAxa.php',
					method: 'POST',
					param: (Ext.Join(
						new Array(
							VAR_POST_DATA,
							Ext.Serialize('form_data_payer').getElement(),
							Ext.Serialize('form_data_product').getElement(),
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
							$("#tabs").({selected:0});
							$( "#tabs" ).tabs( "option", "disabled", [0,1,2,3,4]);

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
							$("#tabs").({selected:0});
							$( "#tabs" ).tabs( "option", "disabled", [0,1,2,3,4]);

						} else {
							alert("Failed, Create Polis. please try again !")
						}
					}

				}).post();
			}
		}
		else{
			alert('Input Beneficiary not complete!');
			return false;
		}
    } else {
        alert("Premium group "+(Ext.Cmp("InsuredGroupPremi").getValue()==2?'Main Insured':Ext.Cmp("InsuredGroupPremi").getValue()==3?'Spouse':'Dependent')+", already exist!");
        return false;
    }
};