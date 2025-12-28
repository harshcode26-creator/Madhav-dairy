/*------------------------------------------- ALL PRODUCTS CODES ------------------------------------------*/

//HELPER FUNCTIONS
function getImage(id){
  if (id === "1") return "assests/Milk.jpg";
  if (id === "2") return "assests/Milk.jpg";
  if (id === "3") return "assests/paneer.jpg";
  if (id === "4") return "assests/Milk.jpg";
  if (id === "5") return "assests/Milk.jpg";

  return "assests/milk.jpg";
}
function getDesc(id){
  if (id === "1") return "Fresh Milk";
  if (id === "2") return "Pure Ghee";
  if (id === "3") return "High Quality Paneer";
  if (id === "4") return "Fresh And Pure Dahi";
  if (id === "5") return "Tasty Buttermilk";

  return "Fresh Product";
}
function productCard(product){
  return `
    <div class="product">
        <div class="image-box">
            <img src="${getImage(product.id)}" alt="${product.name} loading="lazy"">
        </div>

        <div class="title">${product.name}</div>
        <div class="desc">${getDesc(product.id)}</div>

        <div class="bottom-row">
            <div class="price">₹${product.price}</div>
            <div class="add-btn">+</div>
        </div>
    </div>
  `;
}

//PRODUCTS FETCH FROM DATABSE AND RENDER IT IN UI
async function showProducts() {
  try {
    const res = await fetch("http://localhost/madhav-dairy/madhav-backend/api/products/get-products.php");
    const data = await res.json();

    if (data.status !== "success") return;

    const milkContainer = document.querySelector(".milkContainer");
    const curdContainer = document.querySelector(".curdContainer");
    const processedContainer = document.querySelector(".processedContainer");

    let milkcontainerclutter = "";
    let curdcontainerclutter = "";
    let proccesedcontainerclutter = "";

    data.products.forEach(product => {
      if (product.category_id == 1) {
        milkcontainerclutter += productCard(product);
      }else if (product.category_id == 2) {
        curdcontainerclutter += productCard(product);
      }else if (product.category_id == 3) {
        proccesedcontainerclutter += productCard(product);
      }

      milkContainer.innerHTML = milkcontainerclutter;
      curdContainer.innerHTML = curdcontainerclutter;
      processedContainer.innerHTML = proccesedcontainerclutter;
    });

  } catch (error) {
    console.error("Error loading products:", error);
  }
}
showProducts();

/*------------------------------------------- ALL PRODUCTS CODES ------------------------------------------*/
