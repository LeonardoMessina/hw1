function checkPage1(){
    let isOk=true;
    document.getElementById('nextButton').disabled = true;
    isOk&=checkLength(document.querySelector("input[name='username']"),4,16);
    isOk&=checkLength(document.querySelector("input[name='password']"),8,16);
    isOk&=checkLength(document.querySelector("input[name='email']"),5,50);

    let data = new FormData(); //Preferisco usare FormData perchè così posso usare lo stesso codice sia per i controlli che per il submit finale
    data.append('step','0');
    data.append('username', document.querySelector("input[name='username']").value);
    data.append('password', document.querySelector("input[name='password']").value);
    data.append('confirm_password', document.querySelector("input[name='confirm_password']").value);
    data.append('email', document.querySelector("input[name='email']").value);

    fetch('services/signup_check.php',{
        method: 'POST',
        body:data
    }).then(function(response){
        if(response.ok){
            response.text().then(function(text){
                const err=document.querySelector("#errors p");
                err.textContent='';
                try{
                    const errorsList=JSON.parse(text);
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
                    document.getElementById('nextButton').disabled = !isOk;
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

function checkPage2(event){
    let isOk=true;
    document.getElementById('nextButton').disabled = true;
    isOk&=checkLength(document.querySelector("input[name='telefono1']"),8,15);
    isOk&=checkLength(document.querySelector("input[name='telefono2']"),0,15);
    isOk&=checkLength(document.querySelector("input[name='nomeMuseo']"),4,30);
    isOk&=checkLength(document.querySelector("input[name='dataApertura']"),1,255);

    isOk&=checkSelect(document.querySelector("select[name='tipoMuseo']"));
    isOk&=checkSelect(document.querySelector("select[name='provinciaMuseo']"));
    isOk&=checkSelect(document.querySelector("select[name='cittaMuseo']"));
    isOk&=checkSelect(document.querySelector("select[name='museoPubblicoPrivato']"));

    checkUpload();

    let data = new FormData();
    data.append('step','1');
    data.append('telefono1', document.querySelector("input[name='telefono1']").value);
    data.append('telefono2', document.querySelector("input[name='telefono2']").value);
    data.append('nomeMuseo', document.querySelector("input[name='nomeMuseo']").value);
    data.append('dataApertura', document.querySelector("input[name='dataApertura']").value);
    
    data.append('tipoMuseo', document.querySelector("select[name='tipoMuseo']").value);
    data.append('provinciaMuseo', document.querySelector("select[name='provinciaMuseo']").value);
    data.append('cittaMuseo', document.querySelector("select[name='cittaMuseo']").value);
    data.append('museoPubblicoPrivato', document.querySelector("select[name='museoPubblicoPrivato']").value);

    fetch('services/signup_check.php',{
        method: 'POST',
        body:data
    }).then(function(response){
        if(response.ok){
            response.text().then(function(text){
                const err=document.querySelector("#errors p");
                err.textContent='';
                try{
                    const errorsList=JSON.parse(text);
                    if(errorsList.length===0)
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

                    if(isOk)
                        isOk&=checkUpload();

                    document.getElementById('nextButton').disabled = !isOk;
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

function checkProvinciaMuseo(event){
    const input = event.currentTarget;
 
    fetch('services/cities.php?sigla=' + input.value).then(function(response){
        if(response.ok){
            response.text().then(function(text){
                document.getElementById('cittaMuseo').innerHTML=text;
            });
        }else{
            console.error(response.statusText);
        }
    },function(error){    
        console.error(response.statusText);
    });

    checkPage2(event);
}

document.getElementById('username').addEventListener('input', checkPage1);
document.getElementById('confirm_password').addEventListener('input', checkPage1);
document.getElementById('password').addEventListener('input', checkPage1);
document.getElementById('email').addEventListener('input', checkPage1);
document.getElementById('telefono1').addEventListener('input', checkPage2);
document.getElementById('telefono2').addEventListener('input', checkPage2);
document.getElementById('nomeMuseo').addEventListener('input', checkPage2);
document.getElementById('dataApertura').addEventListener('input', checkPage2);

document.getElementById('tipoMuseo').addEventListener('change', checkPage2);
document.getElementById('provinciaMuseo').addEventListener('change', checkProvinciaMuseo);
document.getElementById('cittaMuseo').addEventListener('change', checkPage2);
document.getElementById('museoPubblicoPrivato').addEventListener('change', checkPage2);

document.getElementById('upload').addEventListener('click', clickSelectFile);
document.getElementById('upload_original').addEventListener('change', checkPage2);