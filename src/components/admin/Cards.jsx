import React, { useEffect, useState } from "react";
import axios from "axios";
import ReactLoading from "react-loading";
import { toast } from "react-toastify";

import "./Cards.scss";
import { Download } from "lucide-react";

const Cards = ({ card, updateCards }) => {
  const [loading, setLoading] = useState(false);
  const url = appLocalizer.apiUrl;

  const notify = (status, msg) => {
    if (status === true) {
      toast.success(msg);
      // getApiPlugins(cardsUrl);
    }
    if (status === false) {
      toast.error(msg);
    }
    if (status === "wait") {
      toast.warn(msg);
    }
  };

  const getApiPlugins = async (url, formData) => {
    try {
      setLoading(true);
      const resp = await axios.post(url, formData);
      const data = resp.data;

      if (data.status === true) {
        toast.success(data.msg);
        updateCards(data.processedResults);
      }
      if (data.status === false) {
        toast.error(data.msg);
      }
      if (data.status === "wait") {
        toast.warn(data.msg);
      }

      console.log(data);
    } catch (error) {
      console.log(error);
    } finally {
      setLoading(false);
    }
  };
  const handleActivateLicense = () => {
    // Redirecionar para a página desejada
    window.location.href =
      window.location.origin +
      "/wp-admin/admin.php?page=lnd-master-dev_license";
  };

  const handleProcess = (action) => {
    const data = {
      action: action,
      type: card.type,
      name: card.path,
      version: card.version,
      itens: card.itens,
      filepath: card.filepath,
      item_name: card.item_name,
    };
    const formData = new FormData();

    Object.entries(data).forEach(([key, value]) => {
      formData.append(key, value);
    });

    const cardsUrl = `${url}`;
    getApiPlugins(cardsUrl, formData);
  };

  const renderButton = () => {
    switch (card.buttons.buttonCard) {
      case "install":
        return (
          <button
            type="button"
            className="text-center card-button-install"
            data-id={card.id}
            data-action={"lnd_install_itens"}
            onClick={() => handleProcess("lnd_install_itens")}
            disabled={loading}
          >
            {loading ? (
              <div
                style={{
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "space-evenly",
                }}
              >
                Instalando...
                <ReactLoading
                  type={"spinningBubbles"}
                  color={"#fff"}
                  height={"7%"}
                  width={"7%"}
                />
              </div>
            ) : (
              "Instalar"
            )}
          </button>
        );
      case "update":
        return (
          <button
            type="button"
            className="card-button-update"
            data-id={card.id}
            data-action={"lnd_update_itens"}
            onClick={() => handleProcess("lnd_update_itens")}
            disabled={loading}
          >
            {loading ? (
              <div
                style={{
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "space-evenly",
                }}
              >
                Atualizando...
                <ReactLoading
                  type={"spinningBubbles"}
                  color={"#fff"}
                  height={"7%"}
                  width={"7%"}
                />
              </div>
            ) : (
              "Atualizar"
            )}
          </button>
        );
      case "activate":
        return (
          <button
            type="button"
            className="card-button-activate"
            data-id={card.id}
            onClick={() => handleProcess("lnd_activate_itens")}
            disabled={loading}
          >
            {loading ? (
              <div
                style={{
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "space-evenly",
                }}
              >
                Ativando...
                <ReactLoading
                  type={"spinningBubbles"}
                  color={"#fff"}
                  height={"7%"}
                  width={"7%"}
                />
              </div>
            ) : (
              "Ativar"
            )}
          </button>
        );
      case "activated":
        return (
          <button
            type="button"
            className="card-button-active"
            data-id={card.id}
            disabled
            onClick={() => {}}
          >
            Ativado
          </button>
        );
      default:
        return (
          <button
            type="button"
            className="card-button-activate-key"
            data-id={card.id}
            onClick={handleActivateLicense}
          >
            ativar license
          </button>
        );
    }
  };

  return (
    <div className="cardContainer">
      <div className="card-sl">
        <div className="card-image">
          <img src={card.img} />
        </div>
        <div className="card-downloads">
          <a className="card-action" href={card.buttons.buttonDownload}>
          <Download size={20} strokeWidth={1.75} absoluteStrokeWidth />
          </a>
        </div>
        <div dangerouslySetInnerHTML={{ __html: card.versionCard }} />
        <div className="card-heading">
          {card.item_name} #{card.id}
        </div>
        <div className="card-text">
          <p>{card.description}</p>
        </div>
      </div>
      {renderButton()}
    </div>
  );
};

export default Cards;
