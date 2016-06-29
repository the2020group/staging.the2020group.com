// JavaScript Document
jQuery(function($) {
    $('select[multiple="multiple"]').multipleSelect({
        maxHeight: 160,
        placeholder: "Select From"
    });
    $(".optionNoOfPost").change(function(){
       
        var data_type=$(this).attr("data-type");
		
        if($(this).val()=="postrange"){
           
            $(".postRange[data-type='"+data_type+"']").show();
        }
        else if ($(this).val()=="postrangebypostid"){
            $(".postRange[data-type='"+data_type+"']").show();
            $(".postRange[data-type='"+data_type+"']").find("input[name='postTotalCount']").attr('placeholder','Total Posts').show();
        }
        else
            $(".postRange[data-type='"+data_type+"']").hide();
		
    });
	  	
    $(".selectData").on( "change",function()
    {
        var selectedVal=$(this).val();
        var selectedId =$(this).attr("data-loopId");
        if(selectedVal=="new_meta")
        {
            $("#tbColumn"+selectedId).show();
        }
		
        if(selectedVal!="new_meta")
        {
            var selectIndex = $(this).index();
            var isSameVal=false;
            $(".selectData").each(function() 
            {
                console.log($(this).val()+"=this");
                if($(this).val()==selectedVal&&selectedId!=$(this).attr("data-loopId")&&$(this).val()!=="--select--")
                {
                    isSameVal=true;
                    alert("Same value selected, Please chose another field");
                }
            });
			
            if(isSameVal==true)
                $(this).val("--select--");
            
            $("#tbColumn"+selectedId).hide();
        }
    });
	
    $("#postDate").on( "change",function()
    {
        var selectedVal=$(this).val();
        if(selectedVal=="setdate")
            $("#setDate").show();
        else
            $("#setDate").hide();
    });
	
    $("#submitMapping").click(function()
    {
        var isNotSelected=false;
        $(".selectData").each(function() 
        {
            if($(this).val()=="--select--")
                isNotSelected=true;		 	
        });
		
        if(isNotSelected)
        {
            if(confirm("You haven't mapped some of the fields? Are you sure to import post?"))
                return true;
            else
                return false;	
        }
        return true;
    });
	
    $('[title]').qtip();
	
});