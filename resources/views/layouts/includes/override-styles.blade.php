<style>
.swal2-container {
    z-index: 1090 !important;
}

@keyframes ccleh_loader {
    0% {
        width: 0;
        height: 0;
        opacity: 1;
    }
    100% {
        width: 50px;
        height: 50px;
        opacity: 0;
    }
}
.ccleh_loader {
    display: inline-block;
    vertical-align: middle;
    position: relative;
    margin: 10px;
}
.ccleh_loader {
    width: 60px;
    height: 60px;
}
.ccleh_loader:before,
.ccleh_loader:after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0;
    margin: auto;
    border-radius: 50%;
    border: 2px solid #062231;
}
.ccleh_loader:before {
    animation: ccleh_loader 2s linear infinite 0s;
}
.ccleh_loader:after {
    animation: ccleh_loader 2s linear infinite 1s;
}
</style>