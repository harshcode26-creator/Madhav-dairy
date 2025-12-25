// js/auth.js

const API_BASE = "http://localhost/madhav-dairy/madhav-backend/api";

/* ---------------- VALIDATION HELPERS ---------------- */

function isValidEmail(email) {
  const emailRegex =
    /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/;
  return emailRegex.test(email);
}

function isStrongPassword(password) {
  // min 8 chars, 1 upper, 1 lower, 1 number, 1 special char
  const passwordRegex =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-={}[\]|\\:;"'<>,./]).{8,}$/;
  return passwordRegex.test(password);
}

function isValidName(name) {
  const nameRegex = /^[A-Za-z\s]{2,}$/;
  return nameRegex.test(name);
}

/* ---------------- LOGIN ---------------- */

document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");

  if (loginForm) {
    loginForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value;

      if (!email || !password) {
        alert("Email and password are required");
        return;
      }

      if (!isValidEmail(email)) {
        alert("Please enter a valid email address");
        return;
      }

      try {
        const res = await fetch(`${API_BASE}/auth/login.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify({ email, password }),
        });

        const data = await res.json();

        if (data.status === "success") {
          window.location.href = "index.html";
        } else {
          alert(data.message || "Login failed");
        }
      } catch (err) {
        console.error(err);
        alert("Something went wrong");
      }
    });
  }

  checkAuthState();
});

/* ---------------- CHECK LOGIN STATE ---------------- */

async function checkAuthState() {
  const authBtn = document.getElementById("authButton");
  if (!authBtn) return;

  try {
    const res = await fetch(`${API_BASE}/auth/profile.php`, {
      credentials: "include",
    });

    const data = await res.json();

    if (data.status === "success") {
      authBtn.textContent = "Logout";
      authBtn.href = "#";
      authBtn.onclick = logoutUser;
    } else {
      authBtn.textContent = "Login / Signup";
      authBtn.href = "login.html";
    }
  } catch (err) {
    console.error("Auth check failed");
  }
}

/* ---------------- LOGOUT ---------------- */

async function logoutUser(e) {
  e.preventDefault();

  try {
    await fetch(`${API_BASE}/auth/logout.php`, {
      credentials: "include",
    });
    window.location.reload();
  } catch (err) {
    console.error("Logout failed");
  }
}

/* ---------------- SIGNUP ---------------- */

document.addEventListener("DOMContentLoaded", function () {
  const signupForm = document.getElementById("signupForm");

  if (signupForm) {
    signupForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("signupEmail").value.trim();
      const password = document.getElementById("signupPassword").value;
      const confirmPassword = document.getElementById("confirmPassword").value;

      if (!name || !email || !password || !confirmPassword) {
        alert("All fields are required");
        return;
      }

      if (!isValidName(name)) {
        alert("Name must contain only letters and spaces");
        return;
      }

      if (!isValidEmail(email)) {
        alert("Please enter a valid email address");
        return;
      }

      if (!isStrongPassword(password)) {
        alert(
          "Password must be at least 8 characters and include uppercase, lowercase, number, and special character"
        );
        return;
      }

      if (password !== confirmPassword) {
        alert("Passwords do not match");
        return;
      }

      try {
        const res = await fetch(`${API_BASE}/auth/register.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify({ name, email, password }),
        });

        const data = await res.json();

        if (data.status === "success") {
          alert("Account created successfully. Please login.");
          window.location.href = "login.html";
        } else {
          alert(data.message || "Signup failed");
        }
      } catch (err) {
        console.error(err);
        alert("Something went wrong");
      }
    });
  }
});

/* ---------------- ADMIN LOGIN ---------------- */

document.addEventListener("DOMContentLoaded", function () {
  const adminForm = document.getElementById("adminLoginForm");

  if (adminForm) {
    adminForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const email = document.getElementById("adminEmail").value.trim();
      const password = document.getElementById("adminPassword").value;

      if (!email || !password) {
        alert("Email and password are required");
        return;
      }

      if (!isValidEmail(email)) {
        alert("Please enter a valid email address");
        return;
      }

      try {
        const res = await fetch(`${API_BASE}/auth/login.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify({ email, password }),
        });

        const data = await res.json();

        if (data.status === "success") {
          if (data.user.role === "superadmin") {
            alert("Login as Super Admin successful");
          } else if (data.user.role === "admin") {
            alert("Login as Admin successful");
          } else {
            alert("You are not authorized as admin");
            await fetch(`${API_BASE}/auth/logout.php`, {
              credentials: "include",
            });
          }
        } else {
          alert(data.message || "Login failed");
        }
      } catch (err) {
        console.error(err);
        alert("Something went wrong");
      }
    });
  }
});


document.addEventListener("DOMContentLoaded", function () {
  const passwordBoxes = document.querySelectorAll(".password-box")

  passwordBoxes.forEach(box => {
    const input = box.querySelector("input")
    const eyeOff = box.querySelector(".ri-eye-off-line")
    const eyeOn = box.querySelector(".ri-eye-line")

    eyeOff.addEventListener("click", () => {
      input.type = "text"
      eyeOff.classList.add("hidden")
      eyeOn.classList.remove("hidden")
    })

    eyeOn.addEventListener("click", () => {
      input.type = "password"
      eyeOn.classList.add("hidden")
      eyeOff.classList.remove("hidden")
    })
  })
})
