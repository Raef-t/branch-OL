// ignore_for_file: use_build_context_synchronously
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:image_picker/image_picker.dart';
import '/core/border_radius/circulars.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_two_texts_component.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';
import '/core/components/text_medium16_component.dart';
import '/core/components/text_medium18_component.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/paddings/padding_without_child/symmetric_padding_without_child.dart';
import '/core/sized_boxs/heights.dart';
import '/core/sized_boxs/widths.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/core/styles/colors_style.dart';
import '/features/auth/presentation/view/widgets/custom_text_form_field_with_direction_in_auth_view.dart';
import '/features/auth/presentation/view/widgets/custom_text_up_field_in_auth_view.dart';
import '/features/profile/presentation/managers/cubits/edit_first_and_last_name_employee/employee_cubit.dart';
import '/features/profile/presentation/managers/cubits/edit_first_and_last_name_employee/employee_state.dart';
import '/features/profile/presentation/managers/cubits/edit_photo_employee/photo_employee_cubit.dart';
import '/features/profile/presentation/managers/cubits/logout/log_out_cubit.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class CustomProfileViewBody extends StatefulWidget {
  const CustomProfileViewBody({super.key});

  @override
  State<CustomProfileViewBody> createState() => _CustomProfileViewBodyState();
}

class _CustomProfileViewBodyState extends State<CustomProfileViewBody> {
  String selectedBranch = 'الفرقان';
  TextEditingController firstNameTextEditingController =
      TextEditingController();
  TextEditingController lastNameTextEditingController = TextEditingController();
  final ImagePicker imagePicker = ImagePicker();
  File? selectedImage; // this will be used later in PUT request
  Future<void> pickImageFromGallery() async {
    final XFile? pickedFile = await imagePicker.pickImage(
      source: ImageSource.gallery,
      imageQuality: 70,
    );
    if (pickedFile != null) {
      setState(() => selectedImage = File(pickedFile.path));
    }
  }

  @override
  void dispose() {
    firstNameTextEditingController.dispose();
    lastNameTextEditingController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return CustomScrollView(
      slivers: [
        const SliverAppBarToHoleAppComponent(
          appBarWidget: AppBarWidgetWithRightArrowImageAndTwoTextsComponent(
            firstText: 'بروفايل',
            secondText: 'يمكنك الاطلاع على معلوماتك الشخصية',
          ),
        ),
        SliverFillRemaining(
          hasScrollBody: false,
          child: BackgroundBodyToViewsComponent(
            child: Column(
              children: [
                BlocBuilder<EmployeeCubit, EmployeeState>(
                  builder: (context, state) {
                    if (state is EmployeeSuccessState) {
                      final photo = state.employeeModel.photoUrl ?? '';
                      return OnlyPaddingWithChild.left55AndRight35(
                        context: context,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Heights.height42(context: context),
                            Center(
                              child: SizedBox(
                                height: 135,
                                width: 135,
                                child: ClipOval(
                                  child: photo.isNotEmpty
                                      ? Image.network(photo, fit: BoxFit.fill)
                                      : Image.network(
                                          'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpR2mt4DTP5bMkhpMu1eMde4Rg6EFc78CfIg&s',
                                          fit: BoxFit.fill,
                                        ),
                                ),
                              ),
                            ),
                            Center(
                              child: Padding(
                                padding: EdgeInsets.only(
                                  left: size.width * 0.244,
                                ),
                                child: GestureDetector(
                                  onTap: pickImageFromGallery,
                                  child: Assets.images.editProfileImage.image(),
                                ),
                              ),
                            ),
                            Heights.height26(context: context),
                            const CustomTextUpFieldInAuthView(text: 'الاسم'),
                            Heights.height7(context: context),
                            CustomTextFormFieldWithDirectionInAuthView(
                              hintText: 'الاسم',
                              textEditingController:
                                  firstNameTextEditingController,
                            ),
                            Heights.height15(context: context),
                            const CustomTextUpFieldInAuthView(text: 'الكنيه'),
                            Heights.height7(context: context),
                            CustomTextFormFieldWithDirectionInAuthView(
                              hintText: 'الكنيه',
                              textEditingController:
                                  lastNameTextEditingController,
                            ),
                          ],
                        ),
                      );
                    } else if (state is EmployeeFailureState) {
                      return FailureStateComponent(
                        errorText: state.errorMessage,
                      );
                    } else if (state is EmployeeLoadingState) {
                      return const CircleLoadingStateComponent();
                    } else {
                      return OnlyPaddingWithChild.left55AndRight35(
                        context: context,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Heights.height42(context: context),
                            Center(
                              child: SizedBox(
                                height: 135,
                                width: 135,
                                child: ClipOval(
                                  child: selectedImage != null
                                      ? Image.file(
                                          selectedImage!,
                                          fit: BoxFit.fill,
                                        )
                                      : Image.network(
                                          'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpR2mt4DTP5bMkhpMu1eMde4Rg6EFc78CfIg&s',
                                          fit: BoxFit.fill,
                                        ),
                                ),
                              ),
                            ),
                            Padding(
                              padding: EdgeInsets.only(left: size.width * 0.18),
                              child: Align(
                                alignment: Alignment.center,
                                child: GestureDetector(
                                  onTap: pickImageFromGallery,
                                  child: Assets.images.editProfileImage.image(),
                                ),
                              ),
                            ),
                            Heights.height26(context: context),
                            const CustomTextUpFieldInAuthView(text: 'الاسم'),
                            Heights.height7(context: context),
                            CustomTextFormFieldWithDirectionInAuthView(
                              hintText: 'الاسم',
                              textEditingController:
                                  firstNameTextEditingController,
                            ),
                            Heights.height15(context: context),
                            const CustomTextUpFieldInAuthView(text: 'الكنيه'),
                            Heights.height7(context: context),
                            CustomTextFormFieldWithDirectionInAuthView(
                              hintText: 'الكنيه',
                              textEditingController:
                                  lastNameTextEditingController,
                            ),
                          ],
                        ),
                      );
                    }
                  },
                ),
                Heights.height25(context: context),
                OnlyPaddingWithChild.left55AndRight35(
                  context: context,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      const CustomTextUpFieldInAuthView(text: 'الفرع'),
                      Heights.height7(context: context),
                      Directionality(
                        textDirection: TextDirection.rtl,
                        child: DropdownButtonFormField<String>(
                          icon: Assets.images.mediumBottomArrowImage.image(),
                          initialValue: selectedBranch,
                          decoration: InputDecoration(
                            hintText: selectedBranch,
                            border: OutlineInputBorder(
                              borderRadius: Circulars.circular10(
                                context: context,
                              ),
                              borderSide: BorderSide.none,
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: Circulars.circular10(
                                context: context,
                              ),
                              borderSide: BorderSide.none,
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: Circulars.circular10(
                                context: context,
                              ),
                              borderSide: BorderSide.none,
                            ),
                            fillColor: ColorsStyle.mediumWhiteColor3,
                            filled: true,
                          ),
                          items: [
                            const DropdownMenuItem<String>(
                              value: 'الفرقان',
                              child: Text('الفرقان'),
                            ),
                            const DropdownMenuItem<String>(
                              value: 'حلب الجديدة',
                              child: Text('حلب الجديدة'),
                            ),
                          ],
                          onChanged: (value) =>
                              setState(() => selectedBranch = value!),
                        ),
                      ),
                    ],
                  ),
                ),

                Heights.height62(context: context),
                GestureDetector(
                  onTap: () async {
                    await context.read<LogOutCubit>().logout();
                    await StoreParametersInSharedPreferences.saveStringParameter(
                      stringValue: '',
                      key: keyTokenAuthToUserInSharedPreferences,
                    );
                    pushGoRouterHelper(
                      context: context,
                      view: kSplashViewRouter,
                    );
                  },
                  child: OnlyPaddingWithChild.right35(
                    context: context,
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        const TextMedium16Component(
                          text: 'تسجيل الخروج',
                          color: ColorsStyle.deepRedColor,
                          fontFamily: FontFamily.tajawal,
                        ),
                        Widths.width13(context: context),
                        Assets.images.logOutImage.image(),
                      ],
                    ),
                  ),
                ),
                Heights.height28(context: context),
                GestureDetector(
                  onTap: () async {
                    final userId =
                        await StoreParametersInSharedPreferences.getIntParameter(
                          key: keyUserIdInSharedPreferences,
                        );
                    await context.read<EmployeeCubit>().updateEmployee(
                      employeeId: userId ?? 1,
                      firstName: firstNameTextEditingController.text.trim(),
                      lastName: lastNameTextEditingController.text.trim(),
                    );
                    if (selectedImage != null) {
                      await context.read<EmployeePhotoCubit>().uploadPhoto(
                        employeeId: userId ?? 1,
                        filePath: selectedImage!.path,
                      );
                    }
                  },
                  child: Container(
                    padding: SymmetricPaddingWithoutChild.vertical5(
                      context: context,
                    ),
                    margin: OnlyPaddingWithoutChild.left37AndRight35(
                      context: context,
                    ),
                    alignment: Alignment.center,
                    decoration: BoxDecoration(
                      borderRadius: Circulars.circular10(context: context),
                      gradient: const LinearGradient(
                        begin: Alignment.centerLeft,
                        end: Alignment.centerRight,
                        colors: [
                          ColorsStyle.deepRussetColor,
                          ColorsStyle.deepPinkColor4,
                        ],
                      ),
                    ),
                    child: const TextMedium18Component(
                      text: 'حفظ',
                      fontFamily: FontFamily.tajawal,
                      color: ColorsStyle.whiteColor,
                    ),
                  ),
                ),
                // Heights.height48(context: context),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
