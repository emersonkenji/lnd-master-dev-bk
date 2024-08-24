import React, { useContext, useState } from "react";
// import { AppContext } from "@/lib/context";
import { Button } from "@/components/ui/button";
import {
  Download,
  BugPlay,
  ArrowDownToLine,
  History,
  Milestone,
  Eye,
  TriangleAlert,
  Loader2,
} from "lucide-react"; 
import CardSkeleton from "@/components/dashboard/downloads/CardSkeleton";
const image =
  "https://lojanegociosdigital.local/wp-content/plugins/lnd-master-dev/assets/images/lnd-downloads.jpg";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { NavLink } from "react-router-dom";

function truncateText(text, maxLength) {
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + "...";
}

const Cards = ({ itensCards, loading, enableButtons, userPlans }) => {
  const [loadingButtons, setLoadingButtons] = useState({});
  const handleClick = (card) => {
    if (card.demo) {
      window.open(card.demo, "_blank");
    } else {
      alert("Link da demo não disponível.");
    }
  };

  const url = appLocalizer.download_files;
  const nonce = appLocalizer.nonce;
 console.log(itensCards);
 
    
  
  const handleDownloadAction = (card, e) => {
    setLoadingButtons((prevLoadingButtons) => ({
      ...prevLoadingButtons,
      [card.id]: true,
    }));
    // const downloadUrl = window.location.origin + `/wp-json/files/v1/download/${card.id}`;
    const downloadUrl = url + card.id;
    const params = {
      _ajax_nonce: nonce,
    };
    
    const filepath = card.filepath.split("/");
    let filename = filepath[0] + ".zip"; // Nome padrão caso não consiga extrair
 
 
    fetch(downloadUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(params),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        // Extrair o nome do arquivo do cabeçalho Content-Disposition
        const disposition = response.headers.get("Content-Disposition");
        const matches = disposition.match(
          /filename[^;=\n]*=[\'"]([^\'"]+)[\'"]/i
        );
        if (matches) {
          filename = matches[1];
        }
        return response.blob();
      })
      .then((blob) => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = filename; // Usa o filename extraído
        document.body.appendChild(a); // Adiciona ao DOM para garantir que funcione
        a.click();
        document.body.removeChild(a); // Remove após o clique
        window.URL.revokeObjectURL(url); // Libere o objeto URL
      })
      .catch((error) => {
        console.error("Erro ao baixar o arquivo:", error);
        // Aqui você pode adicionar uma notificação para o usuário
      })
      .finally(() => {
        setLoadingButtons((prevLoadingButtons) => ({
          ...prevLoadingButtons,
          [card.id]: false,
        }));
      });
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const year = date.getFullYear().toString().slice(-2);
    const month = ("0" + (date.getMonth() + 1)).slice(-2);
    const day = ("0" + date.getDate()).slice(-2);
    return `${day}/${month}/${year}`;
  };

  if (loading) {
    return (
      <div className="grid grid-cols-[repeat(auto-fit,_minmax(230px,_1fr))] justify-normal gap-4">
        {[...Array(30)].map((_, index) => (
          <CardSkeleton key={index} />
        ))}
      </div>
    );
  }

  return (
    <div className="grid grid-cols-[repeat(auto-fit,_minmax(230px,_1fr))] justify-normal gap-4">
      {itensCards.map((card) => {
        const cardPlanPriority = card.instance == 0 ? 5 : card.instance != null ? card.instance :   5;
        const canAccess = card.is_free == 1 || userPlans >= cardPlanPriority;

        return (
          <div
            className="border rounded shadow card bg-card text-card-foreground hover:scale-105"
            key={card.id}
          >
            <div className="card-image">
              <img src={card.image || image} alt="" />
              <div className="eye-icon" onClick={() => handleClick(card)}>
                <Eye className="w-9 h-9" />
              </div>
            </div>
            <div className="ribbon ribbon-top-left">
              <span>{card.type}</span>
            </div>
            <div className="ribbon ribbon-top-right">
              <span>Novo</span>
            </div>
            <div className="card-header">
              <div className="card-version card-d-flex">
                <Milestone className="w-4 h-4" />
                <p>{card.version}</p>
              </div>
              <div className="card-update card-d-flex">
                <History className="w-4 h-4" />
                <p>{formatDate(card.update_date)}</p>
              </div>
              <div className="dow-count card-d-flex">
                <ArrowDownToLine className="w-4 h-4" /> {card.count}
              </div>
            </div>
            <div className="card-body">
              <h4>{card.item_name}</h4>
              <div className="card-description">
                <h6>Description</h6>
                <p>{truncateText(card.description, 60)}</p>
              </div>
            </div>
            <div className="card-footer">
              {canAccess ? (
                <>
                  <Button
                    className="w-1/5"
                    type="button"
                    size={"default"}
                    variant={"destructive"}
                    disabled={enableButtons}
                  >
                    <BugPlay className="w-4 h-4" />
                  </Button>
                  <Button
                    className="w-4/5"
                    type="button"
                    onClick={(e) => handleDownloadAction(card, e)}
                    size={"default"}
                    variant={"outline"}
                    disabled={enableButtons}
                  >
                    <span className="mr-2 text-center text-primary">
                      Downloads
                    </span>
                    {loadingButtons[card.id] ? (
                      <Loader2 className="w-4 h-4 animate-spin" />
                    ) : (
                      <Download className="w-4 h-4" />
                    )}
                  </Button>
                </>
              ) : (
                <AlertDialog>
                  <AlertDialogTrigger asChild>
                    <Button
                      className="w-[100%]"
                      type="button"
                      size={"default"}
                      variant={"outline"}
                    >
                      <span className="mr-2 text-center text-red-700">
                        Obter acesso!
                      </span>
                      <TriangleAlert className="w-5 h-5 stroke-red-700" />
                    </Button>
                  </AlertDialogTrigger>
                  <AlertDialogContent>
                    <AlertDialogHeader>
                      <AlertDialogTitle>
                        Obter acesso aos plugins e temas
                      </AlertDialogTitle>
                      <AlertDialogDescription>
                        Para obter acesso completo a nossa biblioteca clique no
                        link ou aperte e continuar.
                        <NavLink
                          to="/prices"
                          className={
                            "ml-1 hover:text-slate-100 hover:scale-100"
                          }
                        >
                          Ver Planos
                        </NavLink>
                      </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                      <AlertDialogCancel>Cancel</AlertDialogCancel>
                      <AlertDialogAction>
                        <NavLink to="/prices">Continuar</NavLink>
                      </AlertDialogAction>
                    </AlertDialogFooter>
                  </AlertDialogContent>
                </AlertDialog>
              )}
            </div>
          </div>
        );
      })}
    </div>
  );
};

export default Cards;
