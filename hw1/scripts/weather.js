function onSuccessWeather(text, divId){
    try{
        const dataWeather=JSON.parse(text);
        const description=document.createElement("h2");
        description.textContent="Meteo : " + dataWeather.data[0].weather.description;
        const divWeather=document.querySelector("#" + divId);
        divWeather.appendChild(description);
        const img=document.createElement("img");
        img.src='https://www.weatherbit.io/static/img/icons/' + dataWeather.data[0].weather.icon + '.png';
        divWeather.appendChild(img);
    }catch(e){
        console.error("onSuccessWeather",e,text);
    }
}

function onErrorWeather(error, divId) {
    const description=document.createElement("h2");
    description.textContent="Errore meteo: " + error;
    const divWeather=document.querySelector("#" + divId);
    divWeather.appendChild(description);
}

function showWeather(id, divId){
    fetch('services/weather.php?id='+id).then(function(response){
        if(response.ok){
            response.text().then(function(text){
                onSuccessWeather(text,divId);
            });
        }else{
            onErrorWeather(response.statusText, divId);
            return null;
        }
    },function(error){    
        onErrorWeather(error, divId);
    });
}
