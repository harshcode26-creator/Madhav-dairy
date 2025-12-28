async function loadLanguage(lang) {
  try {
    document.body.classList.add("lang-switching");

    const res = await fetch(`lang/${lang}.json`);
    const translations = await res.json();

    document.querySelectorAll("[data-i18n]").forEach(el => {
      const key = el.getAttribute("data-i18n");
      if (translations[key]) {
        el.textContent = translations[key];
      }
    });

    // Font switching
    if (lang === "guj") {
      document.body.classList.add("lang-guj");
    } else {
      document.body.classList.remove("lang-guj");
    }

    localStorage.setItem("lang", lang);

    // remove animation state
    setTimeout(() => {
      document.body.classList.remove("lang-switching");
    }, 150);

  } catch (error) {
    console.error("Language load failed", error);
    document.body.classList.remove("lang-switching");
  }
}


document.addEventListener("DOMContentLoaded", () => {
  const langBtn = document.getElementById("langbtn");
  if (!langBtn) return;

  const savedLang = localStorage.getItem("lang") || "en";
  loadLanguage(savedLang);

  langBtn.textContent = savedLang === "en" ? "ગુજરાતી" : "English";

  langBtn.addEventListener("click", () => {
    const currentLang = localStorage.getItem("lang") || "en";

    if (currentLang === "en") {
      loadLanguage("guj");
      langBtn.textContent = "English";
    } else {
      loadLanguage("en");
      langBtn.textContent = "ગુજરાતી";
    }
  });
});
