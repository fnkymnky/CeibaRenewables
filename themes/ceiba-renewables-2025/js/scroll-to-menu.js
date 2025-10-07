const sections = document.querySelectorAll(".scroll-to-block");
const navLinks = document.querySelectorAll(".scroll-to-nav a");

function setActiveLink() {
    let mid = window.innerHeight / 2;
    let current = null;

    sections.forEach(section => {
    const rect = section.getBoundingClientRect();
    if (rect.top <= mid && rect.bottom >= mid) {
        current = section.getAttribute("id");
    }
    });

    navLinks.forEach(link => {
    link.classList.toggle("active", link.getAttribute("href") === `#${current}`);
    });
}

window.addEventListener("scroll", setActiveLink);
window.addEventListener("load", setActiveLink);