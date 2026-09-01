$(document).ready(function(){
	window.setInterval(function(){
		chk_ajx_load(); 
	}, 100);
	window.setInterval(function(){
		chk_ajx_load_full(); 
	}, 500);
	$('body').on('input', 'input.number', function() {
		var value = $(this).val();
		value = value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
		$(this).val(value);
	  });
	  $('body').on('input', 'input.number_sep', function() {
		var value = $(this).val();
		value = rmSep(value);
		$(this).val(sep(value));
	  });
});

ajx_arr=[];
function load_ajx(ajx, elem='body',bgcolor='rgba(255, 255, 255, 0.5)')
{
	if(bgcolor=='dark')
	{
		bgcolor='rgba(0, 0, 0, 0.2)'
	}
	if($(elem + ' > .testdxx').length==0)
	{
		elem_av = 0;
		div_html ='<div  class="testdxx" style="display:none;background-color:'+bgcolor+';position:absolute;z-index:999;text-align: center;">\
						<div class="dvspn">\
						<div class="spinner-border text-primary m-1" role="status" style="scale: 1.5;">\
							<span class="sr-only">Loading...</span>\
						</div></div>\
					</div>';
		$(elem).prepend(div_html);
		ajx_arr[elem]=[];
	}
		load=$(elem+' > .testdxx');
		paren=$(elem);
		load.width(paren.width());
		load.height(paren.height());
		pdtop=load.height()/2.5;
		load.find('.dvspn').css('margin-top',pdtop+"px");
		load.fadeIn();
	ajx_arr[elem].push(ajx);
}
 
function chk_ajx_load()
{
	for (var key_elem in ajx_arr) 
	{
		all_done=1;
		ob_ajax=ajx_arr[key_elem];
		ob_ajax.forEach(function(obj_ajx,key_ajx)
		{
			if(obj_ajx.readyState!=4)
			{
				all_done=0;
			}
		});
		elem=key_elem;
		load=$(elem+' > .testdxx');
		paren=$(elem);
		load.width(paren.width());
		load.height(paren.height());
		load.find('.dvspn').css('margin-top',pdtop+"px");
		if(all_done==1)
		{
			$(key_elem+' > .testdxx').fadeOut();
			ajx_arr[key_elem]=[];
		}
	};
}

ajx_arr_full=[];
function load_show(ajx)
{
	ajx_arr_full.push(ajx);
	$('#div_load_full').fadeIn();
}

function load_hide()
{
	$('#div_load_full').fadeOut(); 
}

function chk_ajx_load_full()
{
		all_done=1;;
		ajx_arr_full.forEach(function(obj_ajx)
		{
			if(obj_ajx.readyState!=4)
			{
				all_done=0;
			}
		});
		if(all_done==1)
		{
			load_hide();
			ajx_arr_full=[];
		}
}

function ld_show(elem)
{
	$(elem+' .ld_tmp').fadeIn("fast");
}

function ld_hide(elem)
{
	$(elem+' .ld_tmp').fadeOut("fast");
}


function getDay(days=0)
{
	var days; // Days you want to subtract
	var date = new Date();
	var d = new Date(date.getTime() + (days * 24 * 60 * 60 * 1000));
	var hari =("0" + d.getDate()).slice(-2);
	var bulan = ("0" + (d.getMonth() + 1)).slice(-2);
	var tahun = d.getFullYear();
	var tanggal_now = tahun.toString()+'-'+bulan.toString()+'-'+hari.toString();
	return tanggal_now;
}

function tgl_indo(tgl)
{
	tgl = tgl.split('-');
	tgl = tgl[2]+'-'+tgl[1]+'-'+tgl[0];
	return tgl;
}

function timeNow() {
  var d = new Date(),
    h = (d.getHours()<10?'0':'') + d.getHours(),
    m = (d.getMinutes()<10?'0':'') + d.getMinutes();
   time = h + ':' + m;
   return time;
}

function isJson(item) {
    item = typeof item !== "string"
        ? JSON.stringify(item)
        : item;

    try {
        item = JSON.parse(item);
    } catch (e) {
        return false;
    }

    if (typeof item === "object" && item !== null) {
        return true;
    }

    return false;
}

//function to add commas to textboxes
function sep(Num) { 
		Num=parseInt(Num);
		if(isNaN(Num))
		{
			return null;
		}
        Num += '';
        Num = Num.replace('.', ''); Num = Num.replace('.', ''); Num = Num.replace('.', '');
        Num = Num.replace('.', ''); Num = Num.replace('.', ''); Num = Num.replace('.', '');
        x = Num.split(',');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1))
            x1 = x1.replace(rgx, '$1' + '.' + '$2');
        return x1 + x2;
    }

function rmSep(val)
{
	result = val.replace(/\./g, "");
	return parseInt(result);
}

function isValidDate(dateString)
{
    // First check for the pattern
    /* if(!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
        return false; */
    // Parse the date parts to integers
    var parts = dateString.split("-");
    var day = parseInt(parts[0], 10);
    var month = parseInt(parts[1], 10);
    var year = parseInt(parts[2], 10);

    // Check the ranges of month and year
    if(year < 1000 || year > 3000 || month == 0 || month > 12)
        return false;

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
};


function cariable(elem,q='',effect='no')
{
	txt = q;
	txt_arr = txt.split(" ");
	c_item='.cari-item';
	q_item ='.cari-q';
	c_result=0;
	c_count=0;
	if (elem && typeof elem === 'object' && !Array.isArray(elem)) 
	{
		objek=elem;
	}
	else
	{
		objek = $(elem+" "+c_item);
	}
	objek.removeClass('cari-show cari-hide');
	objek.each(
	function()
	{
		s_item=this;
		con_txt='';
		$(s_item).find(q_item).each(
			function()
			{
				con_txt = con_txt+' '+$(this).text();
			}
		);
		c_stat=1;
		txt_arr.forEach(
			function(q)
			{
				if(!con_txt.toUpperCase().includes(q.toUpperCase()) && c_stat==1)
				{
					c_stat=0;
				}
			}
		);
		c_result +=c_stat;
		c_count +=1;
		if(c_stat==0)
		{
			$(s_item).addClass('cari-hide');
		}
		else
		{
			$(s_item).addClass('cari-show');
		}
		if(effect=='slide')
		{
			if(c_stat==0)
			{
				$(s_item).slideUp();
			}
			else
			{
				$(s_item).slideDown();
			}
		}
		else if(effect=='fade')
		{
			if(c_stat==0)
			{
				$(s_item).fadeOut();
			}
			else
			{
				$(s_item).fadeIn();
			}
		}
		else
		{
			if(c_stat==0)
			{
				$(s_item).hide();
			}
			else
			{
				$(s_item).show();
			}
		}
		$(s_item).find('.cari-no').html(c_result);
	});
	dt={};
	dt.result=c_result;
	dt.count=c_count;
	if(txt=='')
	{
		dt.stat='empty_search';
	}
	else if(dt.result==0)
	{
		dt.stat='no_result';
	}
	else
	{
		dt.stat='match_result';
	}
	return dt;
}

function isPrimitiveValue(value) {
        return (
                typeof value === "symbol" ||
                typeof value === "string" ||
                typeof value === "number" ||
                typeof value === "boolean" ||
                typeof value === "undefined" ||
                value === null ||
                typeof value === "bigint"
              );
            };
			
function isObject(input) {
            if (isPrimitiveValue(input)) {
              return false;
            }
            return true;
          };
jQuery.fn.scrollTo = function(elem, speed=500) {
		var $this = jQuery(this);
		var $this_top = $this.offset().top;
		var $this_bottom = $this_top + $this.height();
		var $elem = elem;
		var $elem_top = $elem.offset().top;
		var $elem_bottom = $elem_top + $elem.height()+25;

		if ($elem_top > $this_top && $elem_bottom < $this_bottom) {
			return;
		}
		var new_scroll_top;
		if ($elem_top < $this_top) {
			new_scroll_top = {scrollTop: $this.scrollTop() - $this_top + $elem_top};
		} else {
			new_scroll_top = {scrollTop: $elem_bottom - $this_bottom + $this.scrollTop()};
		}
		$this.animate(new_scroll_top, speed === undefined ? 100 : speed);
		return this;
	};
	

// Cookies
function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";               

    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}
function setCookie(name, days) {
    createCookie(name, name, days);
}
function unsetCookie(name) {
    createCookie(name, "", -1);
}

function getFormData(object) {
    const formData = new FormData();
    Object.keys(object).forEach(key => formData.append(key, object[key]));
    return formData;
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}