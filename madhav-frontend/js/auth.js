// js/auth.js

//alert functions 
function showError(title, message) {
  Swal.fire({
    icon: "error",
    title: title,
    text: message,
    backdrop: `
      rgba(0,0,0,0.2)
      backdrop-filter: blur(4px)
    `
  });
}

function showWarning(title, message) {
  Swal.fire({
    icon: "warning",
    title: title,
    text: message,
    backdrop: `
      rgba(0,0,0,0.2)
      backdrop-filter: blur(4px)
    `
  });
}

function showSuccess(title, message, callback) {
  Swal.fire({
    icon: "success",
    title: title,
    text: message,
    timer: 1500,
    showConfirmButton: false,
    backdrop: `
      rgba(0,0,0,0.2)
      backdrop-filter: blur(4px)
    `
  }).then(() => {
    if (callback) callback();
  });
}


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
        showWarning("Missing Fields", "Email and password are required");
        return;
      }

      if (!isValidEmail(email)) {
        showError("Invalid Email", "Please enter a valid email address");
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
          showSuccess("Login Successful", "Welcome back!", () => {
            window.location.href = "index.html";
          });
        } else {
          showError("Login Failed", data.message || "Invalid credentials");
        }
      } catch (err) {
        showError("Error", "Something went wrong. Please try again.");
      }
    });
  }

  checkAuthState();
});

/* ---------------- CHECK LOGIN STATE ---------------- */

async function checkAuthState() {
  const authBtn = document.getElementById("authButton");
  if (!authBtn) return;

  const currentLang = localStorage.getItem("lang") || "en";

  try {
    const res = await fetch(`${API_BASE}/auth/profile.php`, {
      credentials: "include",
    });

    const data = await res.json();

    if (data.status === "success") {
      authBtn.textContent = currentLang === "guj" ? "લૉગઆઉટ" : "Logout";
      authBtn.href = "#";
      authBtn.onclick = logoutUser;
      return;
    }

    if (data.status === "unverified") {
      Swal.fire({
        icon: "warning",
        title: "Email Not Verified",
        text: "Please verify your email to continue.",
        confirmButtonText: "OK",
        backdrop: `
          rgba(0,0,0,0.2)
          backdrop-filter: blur(4px)
        `
      });

      authBtn.textContent =
        currentLang === "guj" ? "લૉગઆઉટ" : "Logout";
      authBtn.href = "#";
      authBtn.onclick = logoutUser;

      return;
    }

    authBtn.textContent =
      currentLang === "guj" ? "લૉગિન / સાઇન અપ" : "Login / Signup";
    authBtn.href = "login.html";

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
        showWarning("Missing Fields", "All fields are required");
        return;
      }

      if (!isValidName(name)) {
        showError("Invalid Name", "Name must contain only letters and spaces");
        return;
      }

      if (!isValidEmail(email)) {
        showError("Invalid Email", "Please enter a valid email address");
        return;
      }

      if (!isStrongPassword(password)) {
        showError(
          "Weak Password",
          "Password must be at least 8 characters and include uppercase, lowercase, number, and special character"
        );
        return;
      }

      if (password !== confirmPassword) {
        showError("Password Mismatch", "Passwords do not match");
        return;
      }

      Swal.fire({
        title: "Creating account...",
        text: "Please wait while we set things up",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });


      try {
        const res = await fetch(`${API_BASE}/auth/register.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify({ name, email, password }),
        });

        const data = await res.json();

        if (data.status === "success") {
          Swal.fire({
            icon: "success",
            title: "OTP Sent",
            text: "We have sent a 6 digit OTP to your email.",
            confirmButtonText: "OK",
          }).then(() => {
              document.getElementById("signupForm").classList.add("hidden");
              document.getElementById("otpSection").classList.remove("hidden");
              startOtpTimer();

              const googleBtn = document.querySelector(".btn-google");
              const footerText = document.querySelector(".footer-text");

              if (googleBtn) googleBtn.classList.add("hidden");
              if (footerText) footerText.classList.add("hidden");
          });
        } else {
          showError("Signup Failed", data.message || "Unable to create account");
        }
      } catch (err) {
        showError("Error", "Something went wrong. Please try again.");
      }
    });

    checkAuthState();
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const verifyBtn = document.getElementById("verifyOtpBtn");
  if (!verifyBtn) return;

  verifyBtn.addEventListener("click", async function () {
    const otp = document.getElementById("otpInput").value.trim();

    if (otp.length !== 6) {
      showError("Invalid OTP", "Please enter a valid 6 digit OTP");
      return;
    }

    try {
      Swal.fire({
        title: "Verifying OTP...",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      const res = await fetch(`${API_BASE}/auth/verify-otp.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ otp })
      });

      const data = await res.json();

      if (data.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Verified",
          text: "Email verified successfully",
          timer: 1200,
          showConfirmButton: false,
        }).then(() => {
          window.location.href = "index.html";
        });
      } else {
        showError("Verification Failed", data.message);
      }

    } catch (err) {
      showError("Error", "Something went wrong. Please try again.");
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const resendBtn = document.getElementById("resendOtpBtn");
  if (!resendBtn) return;

resendBtn.addEventListener("click", async function () {
  if (resendBtn.classList.contains("disabled")) return;

  try {
    Swal.fire({
      title: "Resending OTP...",
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    const res = await fetch(`${API_BASE}/auth/resend-otp.php`, {
      credentials: "include"
    });

    const data = await res.json();

    if (data.status === "success") {
      Swal.fire({
        icon: "success",
        title: "OTP Sent",
        text: "A new OTP has been sent to your email",
        timer: 1200,
        showConfirmButton: false
      });

      startOtpTimer();        // restart expiry timer
      startResendCooldown();  // start cooldown

    } else {
      showError("Error", data.message);
    }
  } catch (err) {
    showError("Error", "Unable to resend OTP");
  }
});

});
let otpExpirySeconds = 200; // 10 minutes
let otpTimerInterval = null;

function startOtpTimer() {
  const timerEl = document.querySelector("#otpTimer span");
  if (!timerEl) return;

  otpExpirySeconds = 200;
  clearInterval(otpTimerInterval);

  otpTimerInterval = setInterval(() => {
    const minutes = Math.floor(otpExpirySeconds / 60);
    const seconds = otpExpirySeconds % 60;

    timerEl.textContent =
      `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;

    otpExpirySeconds--;

    if (otpExpirySeconds < 0) {
      clearInterval(otpTimerInterval);
      timerEl.textContent = "Expired";
      showWarning("OTP Expired", "Please resend OTP to continue");
    }
  }, 1000);
}

let resendCooldown = 30;
let resendInterval = null;

function startResendCooldown() {
  const resendBtn = document.getElementById("resendOtpBtn");
  if (!resendBtn) return;

  resendBtn.classList.add("disabled");
  resendBtn.textContent = `Resend OTP (${resendCooldown}s)`;

  resendInterval = setInterval(() => {
    resendCooldown--;
    resendBtn.textContent = `Resend OTP (${resendCooldown}s)`;

    if (resendCooldown <= 0) {
      clearInterval(resendInterval);
      resendBtn.classList.remove("disabled");
      resendBtn.textContent = "Resend OTP";
      resendCooldown = 30;
    }
  }, 1000);
}


/* ---------------- ADMIN LOGIN ---------------- */

document.addEventListener("DOMContentLoaded", function () {
  const adminForm = document.getElementById("adminLoginForm");

  if (adminForm) {
    adminForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const email = document.getElementById("adminEmail").value.trim();
      const password = document.getElementById("adminPassword").value;

      if (!email || !password) {
        showWarning("Missing Fields", "Email and password are required");
        return;
      }

      if (!isValidEmail(email)) {
        showError("Invalid Email", "Please enter a valid email address");
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
            showSuccess("Welcome Super Admin", "Login successful", () => {
              window.location.href = "superadmin-dashboard.html";
            });
          } else if (data.user.role === "admin") {
            showSuccess("Welcome Admin", "Login successful", () => {
              window.location.href = "admin-dashboard.html";
            });
          } else {
            showError("Unauthorized", "You are not authorized as admin");
            await fetch(`${API_BASE}/auth/logout.php`, {
              credentials: "include",
            });
          }
        } else {
          showError("Login Failed", data.message || "Invalid credentials");
        }
      } catch (err) {
        console.error(err);
        showError("Error", "Something went wrong. Please try again.");
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

document.addEventListener("DOMContentLoaded", async function () {
  const signupForm = document.getElementById("signupForm");
  if (!signupForm) return;

  try {
    const res = await fetch(`${API_BASE}/auth/profile.php`, {
      credentials: "include",
    });

    const data = await res.json();

    if (data.status === "success") {
      // Email verified, user logged in
      window.location.href = "index.html";
    }
  } catch (err) {
    console.error("Signup auth recheck failed");
  }
});
