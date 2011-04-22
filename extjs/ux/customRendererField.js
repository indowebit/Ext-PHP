/* 
  **  A custom renderer field
  **  by g13013 - (2009)
  **   
  **  the default renderer is set to internal money rendering method 
  **  
  */ 
Ext.ux.customRendererField = Ext.extend(Ext.form.Field, {


/**
*@param: {priceUnit} Money unit Ex : Algerian DZD
*
*/
priceUnit:'DZD',

/**
*@param: {priceDelemiter} Delemiter for the price space or ',' are a valid delemiter
*
*/
priceDelemiter:' ',

/**
*@param: {enableRenderer} set price rendering is enabled or no
*
*/
enableRenderer:true,


/**
*@param: {customRenderer} a custom renderer 
*
*/

customRenderer:undefined,

    initComponent : function(){
		Ext.ux.customRendererField.superclass.initComponent.call(this);
		if (this.priceDelemiter!=',' && this.priceDelemiter!=' ') this.priceDelemiter=' ' 
    },
	
	renderValue :function(action,disable) {//bypass enableRendering verification when desabling rendering
	if (this.enableRenderer || disable) {
		if (this.customRenderer===undefined){
				this.RenderMoney(action) //if customRenderer is undefined call default renderer
			} else {
				this.customRenderer.call(this,action)
			}
	}		
	},
	
	RenderMoney :function(action) {
		if (!action & !this.el.dom.readOnly) {
			this.el.dom.value=this.el.dom.value.replace(/[^0-9.]+/g,'');//replace all alphabetic ans special characters except '.'
		} else {
			value=this.el.dom.value.replace(/[^0-9.]+/g,'');//replace all alphabetic ans special characters except '.'
			this.el.dom.value=SetMoney(value,this.priceDelemiter,this.priceUnit)//render in money format
		}
	},

	onFocus : function(){
		Ext.ux.customRendererField.superclass.onFocus.call(this);
		this.renderValue(false);
	},
	
	onBlur : function(){
		Ext.ux.customRendererField.superclass.onFocus.call(this);
		this.renderValue(true);
	},
	
    setValue : function(v){			
		Ext.ux.customRendererField.superclass.setValue.call(this,v);
		this.renderValue(true)
    },
	
    getValue : function(){
        if(!this.rendered) {
            return this.value;
        }
		this.renderValue(false)
        var v = this.el.getValue();
        if(v === this.emptyText || v === undefined){
            v = '';
        }
		this.renderValue(true)
        return v;
    },
	
	setenableRenderer: function (set) {
		this.enableRenderer=set
		if (set) {
			this.renderValue(true);
		} else {
			this.renderValue(false,true);//disable rendering
		}
	
	}

});
Ext.reg('customrendererfield',Ext.ux.customRendererField)



function SetMoney (price,delemiter,unit){ 
/*
  **  render in Money format with somme adaptation for unit and delemiter changing posibility 
  **    price	  	price
  **    delemiter	delemiter
  **    unit  		unit
  **  returns new String like (100,000.00 DZD) or (200 000.00 DZD)
  */ 
  delemiter=(undefined===delemiter) ? (" ") : (delemiter)
  unit=(undefined===unit) ? (" ") : (unit)
            price = (Math.round((price-0)*100))/100;
            price = (price == Math.floor(price)) ? price + (".00") : ((price*10 == Math.floor(price*10)) ? price + "0" : price);
            price = String(price);
            var ps = price.split('.');
            var whole = ps[0];
            var sub = ps[1] ? ','+ ps[1] : ',00';
            var r = /(\d+)(\d{3})/;
            while (r.test(whole)) {
                whole = whole.replace(r, '$1' + delemiter + '$2');
            }
            price = whole + sub;
            if(price.charAt(0) == '-'){
                return '-$' + price.substr(1);
            }
            return price+" "+unit;
        }
		
/**************************************************************************
*** this function is the simple adaptation of the original php str_replace
*** i find it in the internet
*** that can help for value rendering process
**************************************************************************/
function str_replace(a, b, str) {
    return str_replace2(str, a, b);
}
function str_replace2(SRs, SRt, SRu) {
  /*
  **  Replace a token in a string
  **    s  string to be processed
  **    t  token to be found and removed
  **    u  token to be inserted
  **  returns new String
  */
  SRRi = SRs.indexOf(SRt);
  SRRr = '';
  if (SRRi == -1) return SRs;
  SRRr += SRs.substring(0,SRRi) + SRu;
  if ( SRRi + SRt.length < SRs.length)
    SRRr += str_replace2(SRs.substring(SRRi + SRt.length, SRs.length), SRt, SRu);
  return SRRr;
}

