import styled from "styled-components";

export const NavStyle = styled.div`
  nav {
    z-index: 50;
    position: fixed;
    background-color: white;
    display: flex;
    width: 100%;
    height: 80px;
    left: 0px;
    top: 0px;
    .catogeries {
      display: flex;
      flex: 1;
      flex-direction: row;
      align-items: flex-start;
      height: 56px;
      margin-left: 80px;
      margin-top: 15px;
      list-style: none;
      li {
        padding-left: 16px;
        padding-right: 16px;
        padding-bottom: 20px;
        cursor: pointer;
        &.active {
          border-bottom: solid 3px #5ece7b;
        }
      }
      .nav-links {
        text-decoration: none;
        font-family: "Raleway";
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        text-transform: uppercase;
        line-height: 120%;
        color: black;
        &.active {
          color: #5ece7b;
          font-weight: 600;
        }
      }
    }
    .nav-logo {
      margin-top: 15px;
      align-self: center;
      width: 45px;
      height: 45px;
      flex: 1;
    }
    .cart-logo {
      margin-top: 15px;
      flex: 1;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      margin-right: 110px;

      .dropdown {
        display: inline-block;
        margin-bottom: 10px;
        cursor: pointer;
        .dropbtn {
          font-size: 20px;
          display: inline-block;
          color: black;
          text-align: center;
          padding: 14px 16px;
          text-decoration: none;
        }
        .arrow {
          width: 10px;
          height: 5px;
        }
      }
      .dropdown-content {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 90px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 9;
        .options {
          width: 90px;
          font-size: large;
          cursor: pointer;
        }
      }
      .dropdown-content option {
        color: black;
        padding: 12px 16px;
        display: block;
        text-align: left;
      }
      .dropdown-content option:hover {
        background-color: #f1f1f1;
      }
      .displayed {
        display: block;
      }
      .cart {
        position: relative;
        width: 30px;
        height: 30px;
        .cart-img {
          margin-left: 22px;
          width: 23px;
          height: 23px;
          cursor: pointer;
        }
        .badge {
          width: 23px;
          height: 23px;
          border-radius: 50%;
          background-color: black;
          display: flex;
          justify-content: center;
          align-items: center;
          position: absolute;
          color: #f1f1f1;
          top: -11px;
          right: -30px;
          span {
            font-family: "Roboto", sans-serif;
            font-style: normal;
            font-weight: 700;
            font-size: 14px;
            line-height: 16px;
            display: flex;
            align-items: center;
            text-align: center;
            text-transform: uppercase;
            color: #ffffff;
          }
        }
      }
    }
  }
`;
