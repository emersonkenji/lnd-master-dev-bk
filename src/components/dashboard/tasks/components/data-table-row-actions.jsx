import React, { useContext, useState } from "react";
import { Download, Loader2, TriangleAlert } from "lucide-react";
import { Button } from "@/components/ui/button";
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
// import { AppContext } from "@/lib/context";
// import { taskSchema } from "../data/schema"

export function DataTableRowActions({ row, user }) {
  const [isLoading, setIsLoading] = useState(false);

  const task = row.original;

  const downloadFile = () => {
    setIsLoading(true);
    const originalFileName = task.filename;
    const formattedFileName = originalFileName
      .toLowerCase()
      .replace(/[&–]/g, "-")
      .replace(/\s+/g, "-")
      .replace(/[^a-z0-9-]/g, "")
      .replace(/-+/g, "-")
      .trim();

    const url = appLocalizer.download_templates;
    const nonce = appLocalizer.nonce;
    // const downloadUrl = "https://api.lojanegociosdigital.com.br/wp-json/templates/v1/files/templates_downloads/" + task.id;
    // const downloadUrl =  window.location.origin + `/wp-json/templates/v1/download/${task.id}`;
    const downloadUrl = url + task.id;
    const params = {
      ajax_nonce: nonce,
    };
    fetch(downloadUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(params),
    })
      .then((response) => response.blob())
      .then((blob) => {
        const url = window.URL.createObjectURL(new Blob([blob]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", formattedFileName + ".zip");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      })
      .catch((error) => console.error("Erro ao baixar o arquivo:", error))
      .finally(() => {
        setIsLoading(false);
      });
  };

  if (user.status === "visitor") {
    return (
      <AlertDialog>
        <AlertDialogTrigger asChild>
          <Button
            variant="ghost"
            className="flex h-8 w-8 p-0 data-[state=open]:bg-muted "
            disabled={false}
          >
            <Download className="w-6 h-6 !text-red-700" />
          </Button>
        </AlertDialogTrigger>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>
              Obter acesso aos templates
            </AlertDialogTitle>
            <AlertDialogDescription>
              Para obter acesso completo a nossa biblioteca clique no link ou
              aperte e continuar.
              <NavLink
                to="/prices"
                className={"ml-1 hover:text-slate-100 hover:scale-100"}
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
    );
  }

  return (
    <Button
      variant="ghost"
      className="flex h-8 w-8 p-0 data-[state=open]:bg-muted"
      onClick={downloadFile}
      disabled={isLoading}
    >
      {isLoading ? (
        <Loader2 className="w-6 h-6 animate-spin" />
      ) : (
        <Download className="w-6 h-6" />
      )}
    </Button>
  );
}
