import styled from "styled-components";

export const PhotoGalleryStyle = styled.div`
  .product-images {
    display: flex;
    gap: 5px;
    height: 100%;
    width: 100%;
    .Photo-thumbinals {
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      img {
        object-fit: scale-down;
        width: 100px;
        height: 100px;
        margin-right: 60px;
        cursor: pointer;
      }
    }
    .Photo-Gallery {
      height: 100%;
      width: 100%;
      position: relative;
      .prev {
        position: absolute;
        top: 50%;
        left: 30px;
        width: 20px;
        height: 20px;
        &:hover {
          cursor: pointer;
        }
      }
      .after {
        position: absolute;
        top: 50%;
        right: 30px;
        width: 20px;
        height: 20px;
        &:hover {
          cursor: pointer;
        }
      }
      .outStock {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        top: 0;
        left: 0;
        width: 700px;
        height: 600px;
        background-color: rgba(255, 255, 255, 0.5);
        p {
          text-transform: uppercase;
          font-weight: 400;
          font-size: 40px;
          line-height: 160%;
          font-family: "Raleway";
          color: #8d8f9a;
        }
      }
      img {
        object-fit: scale-down;
        width: 700px;
        height: 600px;
      }
    }
  }
`;
