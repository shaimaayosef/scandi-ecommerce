import styled from "styled-components";

export const CardStyel = styled.div`
  .CardContainer {
    margin-top: 10px;
    position: absolute;
    width: 90%;
    height: 100%;
    left: 0px;
    top: 80px;
    background: #ffffff;
    .title {
      width: 299px;
      height: 68px;
      font-family: "Raleway";
      left: calc(50% - 299px / 2 - 469.5px);
      top: calc(50% - 68px / 2 - 562.5px);
      display: flex;
      align-items: center;
      margin-top: 80px;
      margin-left: 101px;
      margin-bottom: 103px;
      h2 {
        font-style: normal;
        font-weight: 400;
        font-size: 42px;
        line-height: 160%;
        text-transform: capitalize;
      }
    }
    .cardsDiv {
      width: 100%;
      margin-left: 100px;
      margin-right: 100px;
      margin-bottom: 271px;
      list-style: none;
      display: flex;
      flex-wrap: wrap;
    }
  }
  .pro-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 16px;
    width: 386px;
    height: 444px;
    cursor: pointer;

    &:hover {
      box-shadow: 0px 4px 35px rgba(168, 172, 176, 0.19);
      .cart-badge {
        display: flex;
      }
    }
    .outStock {
      display: flex;
      justify-content: center;
      padding-top: 144px;
      padding-left: 10px;
      position: absolute;
      top: 0;
      left: 0;
      width: 386px;
      height: 444px;
      background-color: rgba(255, 255, 255, 0.5);

      p {
        text-transform: uppercase;
        font-weight: 400;
        font-size: 24px;
        line-height: 160%;
        font-family: "Raleway";
        color: #8d8f9a;
      }
    }

    .product-img {
      width: 383px;
      height: 330px;
      object-fit: scale-down;
    }

    .cart-badge {
      position: absolute;
      background: #5ece7b;
      top: 315px;
      right: 30px;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: none;
      align-items: center;
      text-align: center;
      /* margin-top: -26px;
      margin-left: 310px; */
      user-select: none;
      .c-img {
        margin-left: 12px;
        img {
          width: 30px;
          height: 30px;
          color: rgb(255, 255, 255);
        }
      }
    }
    .product-name {
      font-size: 18px;
      margin-bottom: -13px;
      color: rgb(51, 53, 55);
      font-family: "Raleway";
    }
    .price {
      margin-top: 20px;
      margin-bottom: -10px;
      font-weight: 600;
      font-size: 18px;
      font-family: "Raleway";
      text-align: right;
      color: #1d1f22;
    }
  }
`;
