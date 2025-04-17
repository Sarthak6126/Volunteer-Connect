// Select elements only once
const elements = {
    toggleBtns: document.querySelectorAll('#mod-mob, #mod-lap'),
    body: document.body,
    nav: document.querySelector("#nav"),
    div1: document.querySelector("#div1"),
    hed1: document.querySelector("#hed1"),
    h5: document.querySelector("#h5"),
    h6: document.querySelector("#h6")
  };
  
  let isDark = false;
  
  // Common function to toggle theme
  function toggleTheme() {
    isDark = !isDark;
    
    // Elements that change classes based on theme
    const themeChanges = [
      {
        element: elements.body,
        dark: { add: ["bg-gray-900", "text-white"], remove: ["bg-white", "text-black"] },
        light: { add: ["bg-white", "text-black"], remove: ["bg-gray-900", "text-white"] }
      },
      {
        element: elements.nav,
        dark: { replace: ["bg-[rgba(118,83,207,0.91)]", "bg-gray-800"] },
        light: { replace: ["bg-gray-800", "bg-[rgba(118,83,207,0.91)]"] }
      },
      {
        element: elements.div1,
        dark: { replace: ["bg-[rgba(188,189,228,0.78)]", "bg-gray-700"] },
        light: { replace: ["bg-gray-700", "bg-[rgba(188,189,228,0.78)]"] }
      },
      {
        element: elements.hed1,
        dark: { replace: ["text-purple-600", "text-yellow-300"] },
        light: { replace: ["text-yellow-300", "text-purple-600"] }
      },
      {
        element: elements.h5,
        dark: { add: ["text-gray-200"] },
        light: { remove: ["text-gray-200"] }
      },
      {
        element: elements.h6,
        dark: { add: ["text-gray-200"] },
        light: { remove: ["text-gray-200"] }
      }
    ];
  
    // Apply theme changes
    themeChanges.forEach(item => {
      const mode = isDark ? item.dark : item.light;
      
      if (mode.add) mode.add.forEach(cls => item.element.classList.add(cls));
      if (mode.remove) mode.remove.forEach(cls => item.element.classList.remove(cls));
      if (mode.replace) item.element.classList.replace(mode.replace[0], mode.replace[1]);
    });
  
    // Update all toggle buttons
    elements.toggleBtns.forEach(btn => {
      btn.innerHTML = isDark ? "Light" : "Dark";
      
      if (isDark) {
        btn.classList.replace("bg-gray-800", "bg-white");
        btn.classList.replace("text-white", "text-black");
      } else {
        btn.classList.replace("bg-white", "bg-gray-800");
        btn.classList.replace("text-black", "text-white");
      }
    });
  }
  
  // Add event listeners to both buttons
  elements.toggleBtns.forEach(btn => {
    btn.addEventListener("click", toggleTheme);
  });
  
  // Handle responsive display
  function handleResize() {
    const mobileBtn = document.querySelector('#mod-mob');
    if (mobileBtn) {
      if (window.innerWidth >= 768) {
        mobileBtn.classList.remove('block');
      } else {
        mobileBtn.classList.add('block');
      }
    }
  }
  
  window.addEventListener('resize', handleResize);
  // Initialize responsive state
  handleResize();


  