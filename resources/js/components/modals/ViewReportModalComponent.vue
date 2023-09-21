<template>
  <MDBBtn
      color="primary"
      aria-controls="ViewReportModal"
      @click="ViewReportModal=true"
  >
    View Report
  </MDBBtn>
  <MDBModal
      id="ViewReportModal"
      tabindex="-1"
      labelledby="ViewReportModalLabel"
      v-model="ViewReportModal"
  >
    <MDBModalHeader>
      <MDBModalTitle id="exampleModalLabel"> View Report </MDBModalTitle>
    </MDBModalHeader>
    <MDBModalBody>...</MDBModalBody>
    <MDBModalFooter>
      <MDBBtn color="secondary" @click="ViewReportModal = false">Close</MDBBtn>
    </MDBModalFooter>
  </MDBModal>
</template>
<script>
import {
  MDBModal,
  MDBModalHeader,
  MDBModalTitle,
  MDBModalBody,
  MDBModalFooter,
  MDBBtn,
} from 'mdb-vue-ui-kit';
import { ref } from 'vue';
export default {
  components: {
    MDBModal,
    MDBModalHeader,
    MDBModalTitle,
    MDBModalBody,
    MDBModalFooter,
    MDBBtn,
  },
  setup() {
    const ViewReportModal = ref(false);
    return {
      ViewReportModal,
    };
  },
};
</script>