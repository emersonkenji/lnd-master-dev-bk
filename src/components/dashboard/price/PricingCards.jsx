import React from "react";
import { __ } from "@wordpress/i18n";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { CheckIcon } from "lucide-react";

const pricingPlans = [
//   {
//     title: "Free",
//     monthlyPrice: "Free",
//     annualPrice: "Free",
//     description: "Forever free",
//     features: ["1 user", "Plan features", "Product support"],
//     isPopular: false,
//   },
  {
    title: "Basico",
    monthlyPrice: "R$39",
    annualPrice: "R$390",
    description: "All the basics for starting a new business",
    features: ["2 users", "Plan features", "Product support"],
    isPopular: false,
  },
  {
    title: "Gold",
    monthlyPrice: "R$89",
    annualPrice: "R$890",
    description: "Everything you need for a growing business",
    features: ["5 users", "Plan features", "Product support"],
    isPopular: false,
  },
  {
    title: "Diamante",
    monthlyPrice: "R$149",
    annualPrice: "R$1490",
    description: "Advanced features for scaling your business",
    features: ["10 users", "Plan features", "Product support"],
    isPopular: false,
  },
  {
    title: "Completo",
    monthlyPrice: "R$149",
    annualPrice: "R$1490",
    description: "Advanced features for scaling your business",
    features: ["10 users", "Plan features", "Product support"],
    isPopular: true,
  },
];

export default function PricingCards({ isAnnual }) {
  return (
    <div className="grid gap-6 mt-12 sm:grid-cols-2 lg:grid-cols-4 lg:items-center">
      {pricingPlans.map((plan) => (
        <Card key={plan.title} className={plan.isPopular ? "border-primary" : ""}>
          <CardHeader className="pb-2 text-center">
            {plan.isPopular && (
              <Badge className="self-center mb-3 uppercase w-max">
                {__("Most popular", "lnd-master-dev")}
              </Badge>
            )}
            <CardTitle className="mb-7">{__(plan.title, "lnd-master-dev")}</CardTitle>
            <span className="text-5xl font-bold">
              {isAnnual ? plan.annualPrice : plan.monthlyPrice}
            </span>
          </CardHeader>
          <CardDescription className="text-center">
            {__(plan.description, "lnd-master-dev")}
          </CardDescription>
          <CardContent>
            <ul className="mt-7 space-y-2.5 text-sm">
              {plan.features.map((feature) => (
                <li key={feature} className="flex space-x-2">
                  <CheckIcon className="flex-shrink-0 mt-0.5 h-4 w-4" />
                  <span className="text-muted-foreground">{__(feature, "lnd-master-dev")}</span>
                </li>
              ))}
            </ul>
          </CardContent>
          <CardFooter>
            <Button className="w-full" variant={plan.isPopular ? "default" : "outline"}>
              {__("Sign up", "lnd-master-dev")}
            </Button>
          </CardFooter>
        </Card>
      ))}
    </div>
  );
}