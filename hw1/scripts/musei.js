let firstTime=true;
let contents;

function favouriteRemove(event){
    const id=event.currentTarget.parentNode.getAttribute("data-id");
    event.currentTarget.parentNode.remove();
    const correspondingElement=document.querySelector("[data-id='"+id+"']");
    correspondingElement.querySelector(".favouriteButton").classList.remove("hidden");
    const favouritesContent=document.querySelector("#favourites .museumsContainer");
    if(!favouritesContent.querySelector("section")) favouritesContent.parentNode.classList.add("hidden");

    let cookie=getCookie("favourites");
    let ids=cookie.split('|');
    const i=ids.indexOf(id);
    ids.splice(i,1);
    cookie="";
    if(ids.length>=0){
        for(let id of ids){
            if(cookie.length>0)
                cookie+="|";
            cookie+=id;
        }
    }
    setCookie("favourites",cookie);
}

function favouriteAdd(event){
    const favourites=document.querySelector("#favourites");
    favourites.classList.remove("hidden");
    event.currentTarget.classList.add("hidden");
    const id=parseInt(event.currentTarget.parentNode.getAttribute("data-id"));

    for(let museum of contents){
        if(parseInt(museum.id)===id){
            createMuseum(museum,true);
            break;
        }
    }

    let cookie=getCookie("favourites");
    let ids=cookie.split('|');
    const i=ids.indexOf(id.toString());
    if(i<0){
        ids.push(id);
        cookie="";
        if(ids.length>=0){
            for(let id of ids){
                if(cookie.length>0)
                    cookie+="|";
                cookie+=id;
            }
        }
        setCookie("favourites",cookie);
    }
}

let searchTimeout=null;
function searchMuseum(event){
    if(searchTimeout)
        clearTimeout(searchTimeout);

    const searchBar=event.currentTarget;
    searchTimeout=setTimeout(function(){
        searchTimeout=null;
        generation(searchBar.value);
    },1000);
}

function createMuseum(museum, favourite){
    const container=document.querySelector("#"+(favourite ? "favourites" : "museumsList")+" .museumsContainer");
    const section=document.createElement("section");
    section.setAttribute("data-id",museum.id);
    if(favourite){
        section.classList.add("favourite");
    }
    else{ 
        section.classList.add("notFavourite");
    }

    const cookie=getCookie("favourites");
    const ids=cookie.split('|');

    {
        const div=document.createElement("div");
        div.classList.add("favouriteButton");
        if(ids.indexOf(museum.id)>=0 && !favourite)
            div.classList.add("hidden");
        div.addEventListener("click",favourite ? favouriteRemove : favouriteAdd);
        section.appendChild(div);
    }

    {
        const h1=document.createElement("h1");
        h1.textContent=museum.name;
        section.appendChild(h1);
    }

    {
        const img=document.createElement("img");
        img.src=museum.image;
        section.appendChild(img);
    }

    if(museum.coordinate.lat && museum.coordinate.lon){
        const divMap=document.createElement("div");
        divMap.classList.add("map");
        divMap.addEventListener("click", museumMap);
        divMap.title="Mappa ed informazioni sul traffico";
        section.appendChild(divMap);
    }

    {
        const h2City=document.createElement("h2");
        h2City.textContent="Città: " + museum.city;
        section.appendChild(h2City);
    }

    {
        const h2Type=document.createElement("h2");
        h2Type.textContent="Tipo: " + museum.type;
        section.appendChild(h2Type);
    }

    {
        const divWeather=document.createElement("div");
        divWeather.classList.add("meteo");
        divWeather.id='cityMeteo' + museum.id + (favourite ? 'Favourite' : ''); //Diamo ID diversi a seconda che l'elemento sia tra i preferiti o meno per evitare la duplicazione degli ID
        showWeather(museum.id, divWeather.id);
        section.appendChild(divWeather);
    }

    {
        const moreInfoButton=document.createElement("a");
        moreInfoButton.textContent="Clicca per maggiori informazioni!";
        moreInfoButton.href="informazioni_museo.php?id="+museum.id;
        moreInfoButton.classList.add("moreInfoButton");
        section.appendChild(moreInfoButton);
    }

    container.appendChild(section);
}

function onSuccessMusei(text){
    try{
        contents=JSON.parse(text);
    }catch(e){
        console.error("onSuccessMusei",e,text);
    }
    document.querySelector("#museumsList .museumsContainer").textContent='';

    for(let museum of contents)
        createMuseum(museum, false);

    document.getElementById('loading').classList.add('hidden');
    document.getElementById('results').classList.remove('hidden');

    const searchBar=document.querySelector("#museumsList input")
    if(firstTime){
        firstTime=false;

        searchBar.addEventListener("keyup", searchMuseum)

        const cookie=getCookie("favourites");
        if(cookie.length>0){
            const favourites=document.querySelector("#favourites");
            favourites.classList.remove("hidden");

            const ids=cookie.split('|');
            for(let museum of contents){
                if(ids.indexOf(museum.id)>=0){
                    createMuseum(museum,true);
                }
            }
        }
    }
    searchBar.focus();
}

function generation(search){
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('results').classList.add('hidden');
    fetch('services/musei.php?search='+(search ?? "")).then(function(response){
        if(response.ok){
            response.text().then(function(text){
                onSuccessMusei(text);
            });
        }else{
            console.error(response.statusText);
        }
    },function(error){    
        console.error(error);
    });
}

generation();
modalInit();