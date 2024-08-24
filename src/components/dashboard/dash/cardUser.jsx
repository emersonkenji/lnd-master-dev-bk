import React, { useEffect, useState, useContext } from "react";
import { AppContext } from "@/lib/context";
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
} from "@/components/ui/card";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
// import { useOutletContext } from "react-router-dom";
import { Skeleton } from "@/components/ui/skeleton";

const CardUser = () => {
  const { state, updateState } = useContext(AppContext);
  // const { dataUser } = useOutletContext();
  const [isLoading, setIsLoading] = useState(true);
  //  console.log(state.userData);
  useEffect(() => {
    if (state.userData) {
      setIsLoading(false);
    }
  }, [state.userData]);

  const capitalizeFirstLetter = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
  };

  const getFirstLetterCapitalized = (string) => {
    return string.charAt(0).toUpperCase();
  };

  const isLoggedIn = state.userData && state.userData.status === "logged";
  const userName = isLoggedIn
    ? capitalizeFirstLetter(state.userData.data_user.name)
    : "Visitante";
  const userEmail = isLoggedIn
    ? capitalizeFirstLetter(state.userData.data_user.email)
    : "Você precisa estar logado";

  if (isLoading) {
    return (
      <Card className="rounded">
        <CardHeader>
          <div className="flex items-center space-x-2">
            <Skeleton className="h-11 w-[17%] rounded-full bg-gray-200 dark:bg-gray-700" />
            <div className="space-y-2">
              <Skeleton className="h-4 w-[144px] bg-gray-200 dark:bg-gray-700" />
            </div>
          </div>
          <Skeleton className="h-3 w-[100%] bg-gray-200 dark:bg-gray-700" />
        </CardHeader>
      </Card>
    );
  }

  return (
    <Card className="rounded">
      <CardHeader>
        <div className="flex flex-row content-center gap-2">
          <div className="w-20%">
            <Avatar>
              <AvatarImage
                src={isLoggedIn ? state.userData.data_user.avatar : ""}
                alt=""
              />
              <AvatarFallback>
                {isLoggedIn
                  ? getFirstLetterCapitalized(state.userData.data_user.name)
                  : "V"}
              </AvatarFallback>
            </Avatar>
          </div>
          <div className="flex items-center gap-2">
            <CardTitle>{userName}</CardTitle>
          </div>
        </div>
        <CardDescription className="text-xs">{userEmail}</CardDescription>
      </CardHeader>
    </Card>
  );
};

export default CardUser;
