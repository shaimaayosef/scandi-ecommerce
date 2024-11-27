import { gql } from "@apollo/client";

export const GET_CATEGORIES = gql`
  query GetCategories {
    categories {
      name
    }
  }
`;

export const GET_CategoryByName = gql`
  query GetCategoryByName($name: String!) {
    category(name: $name) {
      name
      products {
        id
        name
        inStock
        stock
        gallery {
          product_id
          image_url
        }
        description
        category
        attributes {
          id
          attribute_name
          attribute_type
          items {
            display_value
            value
            item_id
          }
        }
        price {
          amount
          currency_label
          currency_symbol
        }
        brand
      }
    }
  }
`;
export const GET_ProductById = gql`
  query GetProductById($id: String!) {
    product(id: $id) {
      id
      name
      inStock
      stock
      gallery {
        product_id
        image_url
      }
      description
      category
      attributes {
        id
        attribute_name
        items {
          item_id
          attribute_name
          display_value
          value
          product_id
        }
        attribute_type
        product_id
      }
      price {
        amount
        currency_label
        currency_symbol
      }
      brand
      category_id
    }
  }
`;
// GraphQL Mutation for Placing the Order
export const PLACE_ORDER_MUTATION = gql`
  mutation PlaceOrder($orderData: OrderInput!) {
    placeOrder(orderData: $orderData)
  }
`;
