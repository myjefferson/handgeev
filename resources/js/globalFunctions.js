export function copyToClipboard(idInput){
    console.log(idInput)
    let copyText = document.getElementById(idInput);
    // copyText.select();
    // copyText.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(copyText.value);
}