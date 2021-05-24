function onSuccessMap(id,w,h,text){
    let traffics;
    try{
        traffics=JSON.parse(text);
    }catch(e){
        console.error("onSuccessMap",text);
    }
    const p=document.querySelector("#mapModal p");
    p.textContent='';
    for (let traffic of traffics){
        p.append(traffic);
        p.append(document.createElement("br"))
    }
    if(p.textContent.length===0)
        p.textContent="Non ci sono avvisi sul traffico.";

    const img=document.querySelector("#mapModal img");
    img.src='services/map.php?id='+id+'&width='+w+'&height='+h+'&type=image';
    const mapModal=document.querySelector("#mapModal");
    mapModal.classList.remove("hidden");
    const body=document.querySelector("body");
    body.classList.add("no-scroll");
}

function onErrorMap(error) {
    const mapModalError=document.querySelector("#mapModalError");
    mapModalError.classList.remove("hidden");
    const body=document.querySelector("body");
    body.classList.add("no-scroll");
    h2=mapModalError.querySelector("h2");
    h2.textContent=error;
}

function museumMap(event){
    const id=event.currentTarget.parentNode.getAttribute("data-id");
    const width=window.innerWidth;
    const height=window.innerHeight;
    let w=1024;
    let h=512;
    if(width<height){
        w=width;
        h=parseInt(height*.85);
    }
    fetch('services/map.php?id='+id+'&width='+w+'&height='+h+'&type=text').then(function(response){
        if(response.ok){
            response.text().then(function(text){
                 onSuccessMap(id,w,h,text);
            });
        }else{
            onErrorMap(response.statusText);
            return null;
        }
    },function(error){    
        onErrorMap(error);
    });
}