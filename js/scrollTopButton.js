const topBtn = () => {


    document.getElementById("back-to-top").style.scale = window.scrollY >= 1200 ? "1" : "0";
}
    

window.addEventListener("scroll", topBtn);
window.addEventListener("resize", topBtn);