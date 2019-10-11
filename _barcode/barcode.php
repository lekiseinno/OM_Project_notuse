<?php


require_once('lib/BarcodeGenerator.php');
require_once('lib/BarcodeGeneratorPNG.php');
require_once('lib/BarcodeGeneratorSVG.php');
require_once('lib/BarcodeGeneratorJPG.php');
require_once('lib/BarcodeGeneratorHTML.php');

$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

?>
<script type="text/javascript">
function change_form(index,palet){

    //alert(index);
    if(index=='2000'){
        var pdr = $(".indexhid1").val();
        palet=parseInt(palet)-1;
        var txt='งานเกิน';
    }else if(index=='2001'){
        var pdr = $(".indexhid1").val();
        palet=parseInt(palet);
        var txt='งานเสีย';
    }else{
        var pdr = $(".indexhid"+index).val();
        palet=parseInt(palet);  
    }

    //alert(pdr);

    if(palet<10){
            palet='00'+palet;
        }else if(palet<100){
            palet='0'+palet;
        }
    

    if(index=='2000'||index=='2001'){
        index='1';
        
        $('.table1 .big2').html(txt);

        $('.table1 .over').removeClass('hidden');
        $('.table1 img.over ').attr('src','barcode/test_1D.php?code='+pdr+'-'+palet);
        $('.table1 .noover').addClass('hidden');
    }else if(palet=='1'){
        $('.table'+index+' .big2').html('001');
        $('.table'+index+' .big2').html('001');
        $('.table'+index+' .over').addClass('hidden');
        $('.table'+index+' .noover').removeClass('hidden');
    }
    $("table[class!=table"+index+"]").addClass('hidden');
    $('.table'+index).removeClass('hidden');

    var pdr = pdr+'-'+palet;
    console.log(pdr);
    $('img.'+pdr+'-'+palet).attr('src','data:image/png;base64,<?php echo base64_encode($generator->getBarcode('+pdr+', $generator::TYPE_CODE_128)); ?>');
    //alert('barcode/test_1D.php?code='+pdr+'-'+palet+'');

   // if(confirm('confirm to print')){
       $.get('tag_insert.php', { index:index, pdr:pdr, palet:palet})
            .done(function(data) {

                    $('.countdate'+index).html(data);
                    setTimeout(function(){
                        window.print();
                    },1500);   
                    
                
        });
    //}
        //window.print();
    
}
</script>