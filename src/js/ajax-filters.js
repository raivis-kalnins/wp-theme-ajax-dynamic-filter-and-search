jQuery(document).ready(function($){
    // Initialize noUiSlider
    $('.range-slider').each(function(){
        let slider=this;
        let field=$(slider).data('field');
        noUiSlider.create(slider,{
            start:[0,500],
            connect:true,
            range:{'min':0,'max':500},
            tooltips:[true,true],
            format:{to:v=>Math.round(v),from:v=>Number(v)}
        });
        slider.noUiSlider.on('update',function(values){
            $(slider).siblings('div').find('.min-val').text(values[0]);
            $(slider).siblings('div').find('.max-val').text(values[1]);
            fetchProducts();
        });
    });

    let page=1;

    function getFilterData(){
        const data={action:'filter_products',cpt:'product',page:page};
        $('#filters input').each(function(){
            const name=$(this).attr('name');
            if(this.type==='checkbox' && this.checked){
                if(!data[name]) data[name]=[];
                data[name].push(this.value);
            } else if(this.type==='text') data[name]=$(this).val();
        });
        $('.range-slider').each(function(){
            const field=$(this).data('field');
            const vals=$(this)[0].noUiSlider.get();
            data[field]={min:vals[0],max:vals[1]};
        });
        return data;
    }

    function fetchProducts(reset=true){
        if(reset) page=1;
        $.post(ajaxfilters.ajaxurl,getFilterData(),function(response){
            if(reset) $('#results').html(response);
            else $('#results').append(response);
        });
    }

    $('#filters input').on('change',fetchProducts);
    $('#load_more').on('click',function(){ page++; fetchProducts(false); });

    // URL sync
    function updateURL(){
        const params=new URLSearchParams();
        $('#filters input').each(function(){
            if(this.type==='checkbox' && this.checked) params.append(this.name,this.value);
            if(this.type==='text' && this.value) params.append(this.name,this.value);
        });
        $('.range-slider').each(function(){
            const field=$(this).data('field');
            const vals=$(this)[0].noUiSlider.get();
            params.append(field+'_min',vals[0]);
            params.append(field+'_max',vals[1]);
        });
        history.replaceState(null,null,'?'+params.toString());
    }
    $('#filters input').on('change',updateURL);
});