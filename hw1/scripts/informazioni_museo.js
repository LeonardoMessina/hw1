let filterTimeout=null;

function filterOpere(event){
    if(filterTimeout)
        clearTimeout(filterTimeout);

    const inputAnnoIniziale=document.querySelector("input[name='annoIniziale']");
	const inputAnnoFinale=document.querySelector("input[name='annoFinale']");
    filterTimeout=setTimeout(function(){
        filterTimeout=null;
        generation(inputAnnoIniziale.value, inputAnnoFinale.value);
    },1000);
}

function addArtwork(artwork){
	const table=document.querySelector('table tbody');
    const row=document.createElement("tr");
    const rowNome=document.createElement("td");
    const rowAutore=document.createElement("td");
    const rowAnnoInizioCreazione=document.createElement("td");
    const rowAnnoUltimatura=document.createElement("td");
    const rowImmagine=document.createElement("td");
    const rowImmagineImg=document.createElement("img");

    rowNome.textContent=artwork.nome;
    rowAutore.textContent=artwork.autore;
    rowAnnoInizioCreazione.textContent=artwork.anno_inizio_creazione===0 ? "N.D." : Math.abs(artwork.anno_inizio_creazione)+" "+(artwork.anno_inizio_creazione>0 ? "d.C." : "a.C.");
    rowAnnoUltimatura.textContent=artwork.anno_ultimatura===0 ? "N.D." : Math.abs(artwork.anno_ultimatura)+" "+(artwork.anno_ultimatura>0 ? "d.C." : "a.C.");
    rowImmagineImg.classList.add('iconaOpera');
    rowImmagineImg.src=artwork.immagine_opera;

    row.setAttribute("data-id",artwork.id);
    table.appendChild(row);
    row.appendChild(rowNome);
    row.appendChild(rowAutore);
    row.appendChild(rowAnnoInizioCreazione);
    row.appendChild(rowAnnoUltimatura);
    row.appendChild(rowImmagine);
    rowImmagine.appendChild(rowImmagineImg);
}

function onSuccessOpere(text){
	let artworks=[];
    try{
        artworks=JSON.parse(text);
    }catch(e){
        console.error("onSuccessOpere",e,text);
    }
    document.querySelector("table tbody").textContent='';

    for(let artwork of artworks)
		addArtwork(artwork);
}

function generation(annoIniziale,annoFinale){
	const urlParams = new URLSearchParams(window.location.search);
    fetch('services/informazioni_museo.php?idMuseo='+urlParams.get('id')+"&annoInizio="+annoIniziale+"&annoFine="+annoFinale).then(function(response){
        if(response.ok){
            response.text().then(function(text){
                onSuccessOpere(text);
            });
        }else{
            console.error(response.statusText);
        }
    },function(error){    
        console.error(error);
    });
}

document.querySelector("input[name='annoIniziale']").addEventListener('keyup',filterOpere);
document.querySelector("input[name='annoFinale']").addEventListener('keyup',filterOpere);

setNumeric('annoIniziale');
setNumeric('annoFinale');
