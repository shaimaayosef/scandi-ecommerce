import styled from "styled-components";

export const ModalStyle = styled.div`
  width: 100%;
  height: 100%;
  overflow: auto;
  .modal {
    position: fixed;
    z-index: 15;
    padding-top: 100px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0, 0, 0);
    background-color: rgba(0, 0, 0, 0.4);
  }

  .modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 50%;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s;
  }

  @-webkit-keyframes animatetop {
    from {
      top: -300px;
      opacity: 0;
    }
    to {
      top: 0;
      opacity: 1;
    }
  }

  @keyframes animatetop {
    from {
      top: -300px;
      opacity: 0;
    }
    to {
      top: 0;
      opacity: 1;
    }
  }

  .close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
  }

  .modal-body {
    padding: 2px 16px;
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
    p {
      color: black;
      text-transform: uppercase;
      font-weight: 400;
      font-size: 24px;
    }
  }

  .modal-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2px 16px;
    /* background-color: #5cb85c; */
    color: white;
    .btn {
      text-decoration: none;
      font-family: "Raleway";
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2px;
      width: 140px;
      height: 43px;
      background: #5ece7b;
      font-weight: 400;
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
