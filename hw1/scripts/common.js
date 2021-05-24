function modalInit(){
    for(let modal of document.querySelectorAll(".modal")){
        const exitButtonModal=modal.querySelector(".exitButton");
        if(exitButtonModal){
            exitButtonModal.addEventListener("click",exitModalButton);
        }
        modal.addEventListener("click",exitModal);
        const container=modal.querySelector('.container');
        container.addEventListener("click", stopPropagation);
    }
}

function stopPropagation(event){
    event.stopPropagation();
}

function exitModalButton(event){
    const modal=event.currentTarget.parentNode.parentNode;
    modal.classList.add('hidden');
    document.body.classList.remove('no-scroll');
    event.stopPropagation();
}

function exitModal(event){
    const modal=event.currentTarget;
    modal.classList.add('hidden');
    document.body.classList.remove('no-scroll');
    event.stopPropagation();
}

function logout(){
    fetch('services/logout.php').then(function(response){
        if(response.ok){
            document.location="home.php"
        }else{
            return null;
        }
    },function(error){    
    });
}

function setInputFilter(element, inputFilter) {
    ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event){
        element.addEventListener(event,function(){
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = "";
            }
        });
    });
}

function setNumeric(elementId){
    setInputFilter(document.getElementById(elementId),function(value){
        return /^-?\d*\.?\d*$/.test(value);
    });
}

function checkUpload(){
    const upload_original = document.getElementById('upload_original');
    if(upload_original.files.length===0)
        return false;

    document.querySelector('#upload .file_name').textContent = upload_original.files[0].name;
    const o_size = upload_original.files[0].size;
    const mb_size = o_size / 1000000;
    document.querySelector('#upload .file_size').textContent = mb_size.toFixed(2)+" MB";
    const ext = upload_original.files[0].name.split('.').pop();

    if (o_size >= 7000000) {
        return false;
    } else if (!['jpeg', 'jpg', 'png'].includes(ext.toLowerCase()))  {
        return false;
    } else {
        return true;
    }
}

function clickSelectFile() {
    document.getElementById('upload_original').click();
}

function checkLength(input,min,max,acceptNull){
    const result=(acceptNull && input.value.length===0) || (input.value.length>=min && input.value.length<=max);
    return result;
}

function checkSelect(input){
    const result=input.value.length>0;
    return result;
}

function getCookie(name){
    name = name + "=";
    const decodedCookie = decodeURIComponent(document.cookie);
    const ca = decodedCookie.split(';');
    for(let c of ca){
        c=c.trim();
        if(c.indexOf(name)===0)
            return c.substring(name.length,c.length);
    }
    return "";
}
function setCookie(name,value){
    let d=new Date();
    d.setTime(d.getTime()+(90*24*60*60*1000)); //90 giorni
    const expires="expires="+ d.toUTCString();
    document.cookie=name+"="+value+";"+expires+";path=/";    
}

function clearField(element){
    element.value='';
}

function clearUpload(){
    document.getElementById("upload_original").value='';
    document.querySelector("#upload .file_name").textContent="Seleziona un file...";
    document.querySelector("#upload .file_size").textContent="";
}

document.getElementById('logout').addEventListener('click', logout);