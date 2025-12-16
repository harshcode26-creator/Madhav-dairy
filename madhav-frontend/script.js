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


function showProducts(){
    fetch("http://localhost/madhav-dairy/backend/api/products/get-products.php")
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById("container");
    
        if (data.status !== "success") {
          container.innerHTML = "<p>Failed to load products</p>";
          return;
        }

        console.log(data);
        
    
        data.products.forEach(product => {
          container.innerHTML += `
                <div class="product">
                    <div class="image-box">
                        <img src="${getImage(product.id)}" alt="${product.name}">
                    </div>

                    <div class="title">${product.name}</div>
                    <div class="desc">${getDesc(product.id)}</div>

                    <div class="bottom-row">
                        <div class="price">₹${product.price}</div>
                        <div class="add-btn">+</div>
                    </div>
                </div>
          `;
        });
      })
      .catch(err => {
        console.error(err);
      });
}
showProducts();