from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Optional
import searoute as sr

app = FastAPI(
    title="Searoute Microservice",
    description="Kalkulasi rute laut menggunakan library searoute",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


class RouteRequest(BaseModel):
    origin_lon: float
    origin_lat: float
    destination_lon: float
    destination_lat: float
    units: Optional[str] = "km"


class RouteResponse(BaseModel):
    distance: float
    units: str
    geojson: dict


@app.get("/health")
def health_check():
    return {"status": "ok", "service": "searoute-microservice"}


@app.post("/route/sea", response_model=RouteResponse)
def calculate_sea_route(request: RouteRequest):
    """
    Kalkulasi rute laut antara dua koordinat.
    Gunakan format [longitude, latitude].
    """
    origin = [request.origin_lon, request.origin_lat]
    destination = [request.destination_lon, request.destination_lat]

    route = sr.searoute(origin, destination, units=request.units)

    return RouteResponse(
        distance=route["properties"]["length"],
        units=route["properties"]["units"],
        geojson=route
    )
