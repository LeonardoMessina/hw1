function checkPage(event,saveCheck){
    let isOk=true;
    isOk&=checkLength(document.getElementById('nomeMuseo'),4,30);
    isOk&=checkLength(document.getElementById('dataApertura'),1,255);
    isOk&=checkLength(document.getElementById('telefono1'),8,15);
    isOk&=checkLength(document.getElementById('telefono2'),0,15);
    isOk&=checkLength(document.getElementById('nomeSocieta'),3,30,true);
    isOk&=checkLength(document.getElementById('introMuseo'),100,2000,true);

    isOk&=checkSelect(document.getElementById('tipoMuseo'));

    checkUpload();
    
    let data = new FormData();
    data.append('step','0');
    data.append('nomeMuseo', document.querySelector("input[name='nomeMuseo']").value);
    data.append('latitudineMuseo', document.querySelector("input[name='latitudineMuseo']").value);
    data.append('longitudineMuseo', document.querySelector("input[name='longitudineMuseo']").value);
    data.append('costoBiglietto', document.querySelector("input[name='costoBiglietto']").value);
    data.append('dataApertura', document.querySelector("input[name='dataApertura']").value);
    data.append('telefono1', document.querySelector("input[name='telefono1']").value);
    data.append('telefono2', document.querySelector("input[name='telefono2']").value);
    data.append('nomeSocieta', document.querySelector("input[name='nomeSocieta']").value);

    data.append('tipoMuseo', document.querySelector("select[name='tipoMuseo']").value);

    data.append('introMuseo', document.querySelector("textarea[name='introMuseo']").value);

    if(saveCheck && isOk && upload_original.value.length>0)
        data.append('immagineMuseo', upload_original.files[0]);

    fetch('services/gestione_dati_museo.php?type='+(saveCheck ? 'save' : 'check'),{
        method: 'POST',
        body:data
    }).then(function(response){
        if(response.ok){
            response.text().then(function(text){
                const err=document.querySelector("#errors p");
                err.textContent='';
                try{
                    const object=JSON.parse(text);
                    const errorsList=Array.isArray(object) ? object : [];
                    if(!errorsList.length)
                        document.querySelector("#errors>div").classList.add('hidden');
                    else{
                        isOk=false;
                        document.querySelector("#errors>div").classList.remove('hidden');
                    }

                    for(const errorText of errorsList){
                        if(err.textContent.length>0)
                            err.append(document.createElement("br"));
                        err.append(errorText);
                    }

                    if(isOk && saveCheck && typeof object.filePath!=="undefined"){
                        if(object.filePath.length>0)
                            document.getElementById("image").src=object.filePath;
                        isOk=false;
                    }

                    document.getElementById('save').disabled = !isOk;
                }catch(e){
                    err.textContent=e; 
                    err.append(document.createElement("br"));
                    err.append(text);
                    document.querySelector("#errors>div").classList.remove('hidden');
                }
            });
        }
    });
}

setNumeric('latitudineMuseo');
setNumeric('longitudineMuseo');
setNumeric('costoBiglietto');
setNumeric('telefono1');
setNumeric('telefono2');


document.getElementById('nomeMuseo').addEventListener('input', checkPage);
document.getElementById('latitudineMuseo').addEventListener('input', checkPage);
document.getElementById('longitudineMuseo').addEventListener('input', checkPage);
document.getElementById('costoBiglietto').addEventListener('input', checkPage);  
document.getElementById('dataApertura').addEventListener('input', checkPage);
document.getElementById('telefono1').addEventListener('input', checkPage);
document.getElementById('telefono2').addEventListener('input', checkPage);
document.getElementById('nomeSocieta').addEventListener('input', checkPage);
document.getElementById('introMuseo').addEventListener('input', checkPage);
document.getElementById('tipoMuseo').addEventListener('change', checkPage);

document.getElementById('upload').addEventListener('click', clickSelectFile);
document.getElementById('upload_original').addEventListener('change', checkPage);


document.getElementById('save').addEventListener('click', function(event){
    checkPage(event,true);
});
