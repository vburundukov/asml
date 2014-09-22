var NavigationCache = new Array();
var TitleCache = new Array();
// полный путь адреса сайта 
var newURL = window.location.protocol + "//" + window.location.host + "/" + window.location.pathname;
var pathArray = window.location.pathname.split( '/' );
var newPathname = "";

function getUrlVars()
{
	return window.location.href.slice(window.location.href.indexOf('?')).split(/[&?]{1}[\w\d]+=/);
};

newPathname = pathArray[pathArray.length-1];
console.log("newPathname = "+newPathname);
console.log("getUrlVars()= "+getUrlVars()[1]);

function setPage(page) {
	//if ((NavigationCache[page] == 'undefined') || (NavigationCache[page] == null)) {
	var jqxhr = $.get("page.php", { ajaxLoad: true , page: page}, function(data){
	
	$('#ResWorks').html(data);
	NavigationCache[page] = data;
	TitleCache[page] = document.title;
	console.log(page+" = "+document.title);
	history.pushState({page: page, type: "page"}, document.title, 'service.php?page='+page );
	//console.log("Устанавливем в pushState  NavigationCache значение = "+ data);
  });  
  
	jqxhr.error(function() { alert("Ошибка выполнения jqxhr"); });
//} else {console.log(page+" Уже загружен")}
};

function getPage(page) {

		$('#ResWorks').html(NavigationCache[page]);
		$("title").html(TitleCache[page]);
		$('.headtext').html(TitleCache[page]);
		console.log(page+" = "+TitleCache[page]);
		$("input.bwork").attr("page",page);
		
		$("span.works").removeAttr("style");
		$("span.works").each(function(index) {
			if ($(this).attr("page")==page){
				$(this).css({"color":'#FFFFFF',"background-color":'#4F4F4F',"border-bottom":'1px solid #4F4F4F'});
				return false;
			} 
		});
};

// Функция запроса обработки php на сервере (AJAX)
function Getphp(){
	var ResVal=$("input.bwork").attr("page");
	console.log(ResVal);
	var text1,text2,text3,text4,kod2,text5,text6,text7,text8,text9,text10,text11;
	id="contract23"
	$( "body" ).data( "contract23", 52 );
	switch (ResVal) {
		case 'Payout':
			text1=$("input#contract_payment").val().toLowerCase();
			text2=$("input#date1_payment").val();
			text3=$("input#date2_payment").val();
			console.log(text1);
		break;		
		case 'Payment':
			text1=$("input#contract_number").val().toLowerCase();
			console.log(text1);
		break;
		case 'LoanToActive':
			text1=$("input#contract1").val().toLowerCase();
		break;	
		case 'Client':
			text1=$("input#fio").val().toLowerCase();
		break;
		case 'CustomerChange':
			text1 = $("input#borrower_key_change").val();
			text2 = $("input#last_name").val().toLowerCase();	
			text3 = $("input#second_name").val().toLowerCase();
			text4 = $("input#patronimic").val().toLowerCase();
			text5 = $("input#birthday").val();
			text6 = $("input#pass_serial").val();
			text7 = $("input#pass_number").val();
			text8 = $("input#pass_date").val();
			text9 = $("input#who_issue").val().toLowerCase();
			text10 = $("input#pass_code").val();
			text11 = $("input#birthplace").val().toLowerCase();
		break;
		case 'ProcessingHistory':
			text1=$("input#history_loan_key").val().toLowerCase();
		break;
		case 'RemittanceCancel':
			text1=$("input#contract2").val().toLowerCase();
		break;
		case 'ListOfExceptions':
			text1=$("input#borrower_key").val();
			text2=$("select#status_key").val();
			text3=$("input#redmine").val();
		break;
		case 'Discount':
			text1=$("input#borrower_key_discount").val();
			text2=$("select#type_discount").val();
			text3=$("input#redmine_discount").val();
		break;
		case 'ReplacementCard':
			text1=$("input#ean_old").val();
			text2=$("input#ean_new").val();
		break;
		case 'BlackList':
			text1=$("input#borrower_key45").val();
		break;
		case 'UserOffice':
			text1=$("input#fio_1").val().toLowerCase();
		break;
		case 'Geography':
			text1=$("input#locality").val();
		break;
		case 'AddStreet':
			text1=$("input#street_name").val();	
			text2=$("input#sorc1").val();
			text3=$("input#owner_key1").val();
		break;
		case 'AddLocality':
			text1=$("input#locality_name").val();	
			text2=$("input#sorc2").val();
			text3=$("input#owner_key2").val();
		break;
		case 'Doc_nko':
			text1=$("input#nko").val();
		break;
		case 'change_nko':
			text1 = $("input#order_key").val();
			text2 = $("input#order_number").val();	
			text3 = $("input#payment_sum").val();
			text4 = $("input#payment_date").val();
			text5 = $("input#new_loan_key").val();
		break;
		case 'DocReestr':
			text1 = $("input#date_of_transfer_begin").val();
			text2 = $("input#date_of_transfer_end").val();
			text3 = $("select#bank_key1").val();
		break;
		case 'Doc_rko':
			text1=$("input#rko").val();
		break;
		case 'change_rko':
			text1 = $("input#order_key2").val();
			text2 = $("input#fio_number").val().toLowerCase();	
		break;			
		case 'change_RegRec':
			text1 = $("input#order_key_Rec").val();
			text2 = $("input#new_contract_Rec").val().toLowerCase();	
		break;				
		case 'LoanAllClose':
			text1 = $("input#contract_allClose").val();
			text2 = $("input#redmine2_allClose").val().toLowerCase();	
		break;
		case 'LoanСorrectionClose':
			text1 = $("input#contract_Сorrection").val().toLowerCase();
			text2 = $("input#Overpayment_Сorrection").val();	
			text3 = $("input#Fine_Сorrection").val();	
			text4 = $("input#Num_rows_Сorrection").val();	
			text5 = $("input#redmine_Сorrection").val();	
		break;		
		case '33':
			text1=$("input#contract3").val().toLowerCase();
			text2=$("input#redmine2").val().toLowerCase();
		break;
		case 'Process':
			text1=$("input#process_key").val();
		break;
		case 'ProcessTo200':
			text1=$("input#process_key1").val();
		break;
		case 'ProcessTo100':
			text1=$("input#process_key2").val();
		break;				
		case 'ProcessTo103':
			text1=$("input#process_key3").val();
		break;
		case 'ProcessTo14':
			text1=$("input#process_key4").val();
		break;
		case 'ProcessTo99':
			text1=$("input#process_key5").val();
		break;
		case 'ProcessToCancel':
			text1=$("input#process_key6").val();
			text2=$("input#process_key7").val();
		break;
		case 'ProcessToDelete':
			text1=$("input#process_keyDelete ").val();
		break;
		default:
			text1=$("input#fio").val();
			text2='';
		break;
	}
  var now = new Date(); 
  var datetime = now.getFullYear()+'/'+(now.getMonth()+1)+'/'+now.getDate(); 
	datetime += ' '+now.getHours()+':'+now.getMinutes()+':'+now.getSeconds(); 
	startLoadingAnimation();
	$("#Reswork").hide("fast",function () {
		$.ajax({
		   type: "POST",
		   url: "work.php",
		   data:{
		   "ResVal": ResVal,
		   "text1": text1,
		   "text2": text2,
		   "text3": text3,
		   "text4": text4,
		   "text5": text5,
		   "text6": text6,
		   "text7": text7,
		   "text8": text8,
		   "text9": text9,
			"text10": text10,
			"text11": text11
		   },

		  // "ResVal="+ResVal+"&text1="+text1+"&text2="+text2+"&text3="+text3+"&text4="+text4+"&text5="+text5,
		   success: function(html){
					$("#Reswork").removeData().empty();
					$("#Reswork").append(html);
					stopLoadingAnimation();
					$("#StoryDiv").prepend(datetime+"<br>"+text1+"<p>");
					$("#contract23").prepend("<option value='"+text1+"'></option>");
					
				}
		});
	});
	$("#Reswork").show("fast");
	
	
};


$(function(){
		NavigationCache[getUrlVars()[1]] = $('#ResWorks').html();
		// добавляем в адресную строку нужный адрес
		history.pushState({page: getUrlVars()[1], type: "page"}, 'Сервис АСУЗ', newPathname+'?page='+getUrlVars()[1]);

		if (history.pushState) {
			console.log("history.pushState");
			window.addEventListener('popstate', function(event) {
				console.log("event popstate state typeof "+JSON.stringify(event.state)+"!");
				if ((typeof event.state !='undefined') && event.state != null ){
					getPage(event.state.page);
				};
			}, false);
			$('span.navigation').on("click", function(){ 
				setPage($(this).attr('page'));
				return false;
			}) 
		};
		$('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
		$('.tree li.parent_li > span').on('click', function (e) {
			var children = $(this).parent('li.parent_li').find(' > ul > li');
			if (children.is(":visible")) {
				children.hide('fast');
				$(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
			} else {
				children.show('fast');
				$(this).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
			}
			e.stopPropagation();
		});

		  
		var change_e = 0;
		// Клавиша Enter в поле ввода
		$("input.work").bind('keypress',function(e){
			if(e.keyCode==13){
				Getphp();
			}
		});

		// клик по кнопке
		$("input.bwork").bind('click',function(e){
			Getphp();
		});

		$("span.works").click(function(){
			// номер меню
			var ResVal=$(this).attr("page");
			// номер меню
			$("input.bwork").attr("page",ResVal);
			$("input.bwork").attr("value",$(this).attr("data-run"));
			// Выводим номер выбранного меню
			console.log(ResVal);
			var temp1 = $(this).text();
			//console.log(temp1);
			$("h2.headtext").text(temp1);
			$('#headtext').text(temp1);
			$('title').text(temp1);
			$(".Res").animate({opacity: "hide"}, "slow", "linear");
			$(".Res").css({"visibility":'Hidden',"display": 'none'});
			
			$("#Res"+ResVal).animate({ opacity: "show" }, "slow");
			$("#Res"+ResVal).css({"visibility":'visible',"display": 'block'});
		});


	$(".works").click(function(){
		$(".works").removeAttr("style");
		$(this).css({"color":'#FFFFFF',"background-color":'#4F4F4F',"border-bottom":'1px solid #4F4F4F'});
	});

});


	function startLoadingAnimation() // - функция запуска анимации
	{
	  // найдем элемент с изображением загрузки и уберем невидимость:
	  var imgObj = $("#loadImg");
	  imgObj.show();
	  // вычислим в какие координаты нужно поместить изображение загрузки,
	  // чтобы оно оказалось в серидине страницы:
	  var centerY = $(window).scrollTop() + ($(window).height() + imgObj.height())/2;
	  var centerX = $(window).scrollLeft() + ($(window).width() + imgObj.width())/2;
	  // поменяем координаты изображения на нужные:
	  
	  imgObj.offset({top: centerY, left: centerX});
	}
	
	function stopLoadingAnimation() // - функция останавливающая анимацию
	{
	  $("#loadImg").hide();
	}
