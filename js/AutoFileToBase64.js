/**
 * Author: David Rojas Chavarría, Costa Rica
 * Github repository: https://github.com/davidmfrojas/AutoFileToBase64-Plugin
*/
(() => {
     const chooseFileToUpload = (fileChooserId) => {
        const elem = document.querySelector(`#${fileChooserId}`);
        elem.click();
     }

    const toBase64 = (file) => new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });

    const loadSelectedImage = async function (idFile, idImgElement, idInput) {
        const fileTag = document.querySelector(`#${idFile}`);
        const imgTag = document.querySelector(`#${idImgElement}`);
        const inputTag = document.querySelector(`#${idInput}`);
        imgTag.src = await toBase64(fileTag.files[0]);
        inputTag.value = imgTag.src;
    }

    const filesToAutoFileChooser = document.querySelectorAll('[AutofileChooser]');
    filesToAutoFileChooser.forEach((fileChooser) => {
        const inputTagIdToStoreBase64Img = fileChooser.getAttribute("InputTagIdToStoreBase64Img");
        const imgTagIdToDisplay = fileChooser.getAttribute("ImgTagIdToDisplay");
        const imgTagToshowSelectedImage = document.querySelector(`#${imgTagIdToDisplay}`);
        imgTagToshowSelectedImage.addEventListener("click", () => chooseFileToUpload(fileChooser.id));
        fileChooser.addEventListener("change", async () => loadSelectedImage(fileChooser.id, imgTagIdToDisplay, inputTagIdToStoreBase64Img));
    });

})();