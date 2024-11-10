import React, { Component } from "react";
import { gql } from "@apollo/client";
import { ApolloConsumer} from "@apollo/client";

export const GET_CATEGORIES = gql`
  query GetCategories {
    categories {
      name
    }
  }
`;

export const GET_CATEGORY = gql`
  query Category($name: String!) {
    category(name: $name) {
      id
      name
      products {
        id
        name
        inStock
        gallery
        description
        category
        attributes {
          product_id
          attribute_name
          attribute_type
          items {
            displayValue
            value
            item_id
          }
        }
        prices {
          currency {
            currency_label
            currency_symbol
        }
          amount
        }
        brand
      }
    }
  }
`;

export const GET_PRODUCT = gql`
  query product($id: String!) {
    product(id: $id) {
      id
      name
      inStock
      gallery
      description
      category
      attributes {
          product_id
          attribute_name
          attribute_type
          items {
            displayValue
            value
            item_id
          }
        }
      prices {
        currency {
          currency_label
          currency_symbol
        }
        amount
      }
      brand
    }
  }
`;

class App extends Component {
  state = {
    categories: [],
    selectedCategory: null,
    products: [],
    selectedProduct: null,
  };

  fetchCategories = async (client) => {
    try {
      const { data } = await client.query({
        query: GET_CATEGORIES,
      });
      this.setState({ categories: data.categories });
    } catch (error) {
      console.error("Error fetching categories:", error);
    }
  };

  fetchCategoryProducts = async (client, categoryName) => {
    try {
      const { data } = await client.query({
        query: GET_CATEGORY,
        variables: { name: categoryName }, // Correcting this to use 'name' instead of 'input'
      });
      this.setState({
        products: data.category.products,
        selectedCategory: categoryName,
        selectedProduct: null,
      });
    } catch (error) {
      console.error("Error fetching products:", error);
    }
  };

  fetchProductDetails = async (client, productId) => {
    try {
      const { data } = await client.query({
        query: GET_PRODUCT,
        variables: { id: productId },
      });
      this.setState({ selectedProduct: data.product });
    } catch (error) {
      console.error("Error fetching product details:", error);
    }
  };

  render() {
    const { categories, selectedCategory, products, selectedProduct } = this.state;

    return (
      <ApolloConsumer>
      {(client) => (
        <div>
        <nav style={{ padding: "10px", backgroundColor: "#333", color: "#fff" }}>
          <h2>Categories</h2>
          <button onClick={() => this.fetchCategories(client)}>Load Categories</button>
          <div>
          {categories.map((category) => (
            <button
            key={category.name}
            onClick={() => this.fetchCategoryProducts(client, category.name)}
            style={{
              margin: "5px",
              padding: "10px",
              backgroundColor: selectedCategory === category.name ? "#555" : "#444",
              color: "#fff",
              cursor: "pointer",
            }}
            >
            {category.name}
            </button>
          ))}
          </div>
        </nav>

        <div style={{ padding: "20px" }}>
          {selectedCategory && (
          <div style={{ display: "flex", flexWrap: "wrap" }}>
            {products.map((product) => (
            <div
              key={product.id}
              onClick={() => this.fetchProductDetails(client, product.id)}
              style={{
              border: "1px solid #ddd",
              padding: "20px",
              margin: "10px",
              cursor: "pointer",
              width: "200px",
              textAlign: "center",
              }}
            >
              <img
              src={product.gallery[0]}
              alt={product.name}
              style={{ width: "100%", height: "150px", objectFit: "cover" }}
              />
              <h3>{product.name}</h3>
              <p>{product.inStock ? "In Stock" : "Out of Stock"}</p>
              <p>{product.brand}</p>
            </div>
            ))}
          </div>
          )}

          {selectedProduct && (
          <div style={{ marginTop: "20px", padding: "20px", border: "1px solid #ddd" }}>
            <h2>{selectedProduct.name}</h2>
            <img
            src={selectedProduct.gallery[0]}
            alt={selectedProduct.name}
            style={{ width: "100%", height: "300px", objectFit: "cover" }}
            />
            <p>{selectedProduct.description}</p>
            <h3>Brand: {selectedProduct.brand}</h3>
            <p>Category: {selectedProduct.category}</p>
            <h4>Attributes:</h4>
            <ul>
            {selectedProduct.attributes.map((attr) => (
              <li key={attr.product_id}>
              {attr.attribute_name} ({attr.attribute_type})
              <ul>
                {attr.items.map((item) => (
                <li key={item.item_id}>
                  {item.displayValue} ({item.value})
                </li>
                ))}
              </ul>
              </li>
            ))}
            </ul>
            <h4>Prices:</h4>
            <ul>
            {selectedProduct.prices.map((price) => (
              <li key={price.currency.currency_label}>
              {price.currency.currency_symbol} {price.amount} ({price.currency.currency_label})
              </li>
            ))}
            </ul>
          </div>
          )}
        </div>
        </div>
      )}
      </ApolloConsumer>
    );
  }
}

export default App;