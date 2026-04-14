// ignore_for_file: use_build_context_synchronously
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:modal_progress_hud_nsn/modal_progress_hud_nsn.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/build_snack_bar_to_error_text_helper.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/features/auth/presentation/managers/cubits/auth_cubit.dart';
import '/features/auth/presentation/managers/cubits/auth_state.dart';
import '/features/auth/presentation/view/widgets/custom_success_auth_state_in_auth_view.dart';

class CustomAuthViewBody extends StatefulWidget {
  const CustomAuthViewBody({super.key});

  @override
  State<CustomAuthViewBody> createState() => _CustomAuthViewBodyState();
}

class _CustomAuthViewBodyState extends State<CustomAuthViewBody> {
  GlobalKey<FormState> formKey = GlobalKey<FormState>();
  AutovalidateMode autovalidateMode = AutovalidateMode.disabled;
  bool isChecked = false;
  late TextEditingController nameTextEditingController;
  late TextEditingController passwordTextEditingController;
  @override
  void initState() {
    nameTextEditingController = TextEditingController();
    passwordTextEditingController = TextEditingController();
    super.initState();
  }

  @override
  void dispose() {
    nameTextEditingController.dispose();
    passwordTextEditingController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return BlocConsumer<AuthCubit, AuthState>(
      listener: (context, state) {
        if (state is AuthSuccessState) {
          pushGoRouterHelper(context: context, view: kHomeViewRouter);
        } else if (state is AuthFailureState) {
          buildSnackBarToErrorTextHelper(
            context: context,
            errorText: state.errorMessageInCubit,
          );
        }
      },
      builder: (context, state) {
        return ModalProgressHUD(
          inAsyncCall: state is AuthLoadingState,
          child: CustomSuccessAuthStateInAuthView(
            formKey: formKey,
            autovalidateMode: autovalidateMode,
            nameTextEditingController: nameTextEditingController,
            passwordTextEditingController: passwordTextEditingController,
            isChecked: isChecked,
            onChanged: (newValue) => setState(() => isChecked = newValue!),
            loginOnTap: () async {
              if (formKey.currentState!.validate()) {
                await context.read<AuthCubit>().loginMethod(
                  uniqueId: nameTextEditingController.text.trim(),
                  password: passwordTextEditingController.text.trim(),
                );
              } else {
                setState(() => autovalidateMode = AutovalidateMode.always);
              }
            },
          ),
        );
      },
    );
  }
}
