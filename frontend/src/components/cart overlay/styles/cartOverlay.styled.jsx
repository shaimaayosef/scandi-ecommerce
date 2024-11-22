import styled from "styled-components";

export const CartOverlayStyle = styled.div`
  z-index: 10;
  position: fixed;
  top: 80px;
  right: 30px;
  max-height: 600px;
  background: #ffffff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  h3 {
    align-self: flex-start;
    margin-left: 12px;
    font-weight: 700;
    font-size: 16px;
    font-family: "Raleway";
    span {
      font-weight: 500;
      .bold {
        font-weight: bold;
      }
    }
  }
  p {
    font-weight: 500;
    font-size: 16px;
    font-family: "Roboto";
    span {
      font-family: "Raleway";
      font-weight: 700;
    }
  }
  .cart-overlay {
    overflow: auto;
    width: 325px;
    background: #ffffff;
    padding: 10px;
  }
  .btn {
    display: flex;
    .view-btn {
      text-decoration: none;
      border: #0c0c0c solid 1px;
      font-family: "Raleway";
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2px;
      width: 140px;
      font-style: normal;
      font-weight: 600;
      font-size: 14px;
      line-height: 120%;
      text-transform: uppercase;
      background: #ffffff;
      color: #0c0c0c;
      margin: 8px;
      cursor: pointer;
    }
    .check-btn {
      font-family: "Raleway";
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2px;
      width: 300px;
      height: 43px;
      background: #5ece7b;
      font-weight: 600;
      font-size: 14px;
      line-height: 120%;
      text-transform: uppercase;
      color: #ffffff;
      border: none;
      margin: 8px;
      cursor: pointer;
    }
  }
`;
export const CartItemStyle = styled.div`
  .cart-item {
    padding: 5px;
    width: 293px;
    display: flex;
    .product-info {
      margin-right: 20px;
      h2 {
        margin: 4px 0px;
        color: #1d1f22;
        font-style: normal;
        font-weight: 300;
        font-size: 16px;
        font-family: "Raleway";
      }
      .price {
        margin: 2px 0px;
        display: flex;
        flex-direction: column;
        span {
          font-family: "Raleway";
          font-weight: 700;
          font-style: normal;
          font-size: 16px;
          color: #1d1f22;
        }
      }
      .attributes {
        display: flex;
        flex-direction: column;
        width: 130px;
        .size {
          display: flex;
          flex-direction: column;
          h4 {
            margin: 2px 0px;
            font-style: normal;
            font-weight: 400;
            font-size: 14px;
            color: #1d1f22;
            font-family: "Raleway";
          }
          .size-box {
            display: flex;
            flex-wrap: wrap;
            flex: 1;

            .size-x {
              border: 1px solid #1d1f22;
              width: fit-content;
              height: 24px;
              margin: 2px;
              padding: 3px;
              display: flex;
              align-items: center;
              justify-content: center;
              cursor: pointer;
              text-transform: uppercase;
              font-size: 14px;
              font-weight: 400;
              font-family: "Source Sans Pro";
            }
            .selected {
              background-color: #1d1f22;
              color: #ffffff;
            }
          }
        }
        .color {
          display: flex;
          flex-direction: column;
          h4 {
            font-style: normal;
            font-weight: 400;
            font-size: 14px;
            color: #1d1f22;
            font-family: "Raleway";
          }
          .color-box {
            display: flex;
            .color-x {
              border: #1d1f22 solid 1px;
              width: 16px;
              height: 16px;
              background-color: rgba(15, 100, 80, 1);
              margin: 2px;
              cursor: pointer;
              text-transform: uppercase;
              font-size: 14px;
              font-weight: 400;
            }
            .selected {
              border: #5ece7b solid 3px;
            }
          }
        }
      }
    }
    .product-count {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      width: 24px;
      margin-right: 24px;
      .inc {
        width: 24px;
        height: 24px;
        cursor: pointer;
      }
      span {
        font-weight: 500;
        font-size: 16px;
        font-family: "Raleway";
      }
      .dec {
        width: 24px;
        height: 24px;
        cursor: pointer;
      }
    }
    .pro-img {
      display: flex;
      justify-content: center;
      align-items: center;
      height: auto;
      width: 121px;
      position: relative;
      .product-img {
        object-fit: scale-down;
        width: 100%;
        height: 190px;
      }
      .changing-box {
        position: absolute;
        right: 9px;
        bottom: 30px;
        display: flex;
        gap: 8px;
        width: 28px;
        height: 12px;
        .prev {
          cursor: pointer;
        }
        .after {
          cursor: pointer;
        }
      }
    }
  }
`;
